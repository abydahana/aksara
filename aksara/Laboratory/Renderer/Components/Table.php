<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Laboratory\Renderer\Components;

use Config\Services;
use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Builder\Builder;
use Aksara\Laboratory\Renderer\Formatter;
use Aksara\Laboratory\Renderer\Parser;

/**
 * Table Component Renderer
 *
 * This class is responsible for rendering the data table view (index page).
 * It handles column definition, row data formatting, search filters,
 * pagination links, and action buttons (toolbar and row-level).
 */
class Table
{
    /**
     * Load traits to access dynamic properties.
     */
    use Traits;

    /**
     * UI Builder Instance
     */
    private Builder $builder;

    /**
     * Data Formatter Instance
     */
    private Formatter $formatter;

    /**
     * Template Parser Instance
     */
    private Parser $parser;

    /**
     * Current Module Path
     */
    private ?string $path = null;

    /**
     * Database Model
     */
    private mixed $model = null;

    /**
     * API Client Instance
     */
    private mixed $api_client = null;

    /**
     * Collected Field Names
     */
    private array $fields = [];

    /**
     * Valid Field Types for Table Column
     * @var array
     */
    private const VALID_TYPES = [
        'text', 'textarea', 'wysiwyg',
        'number', 'money', 'percent',
        'select', 'checkbox', 'radio', 'boolean', 'range', 'color',
        'date', 'datetime', 'time', 'week', 'month',
        'hidden', 'email', 'password', 'encryption',
        'file', 'files', 'image', 'images',
        'hyperlink',
        'accordion', 'attribution', 'carousel', 'geospatial',
        'custom_format'
    ];

    /**
     * Constructor
     *
     * @param   array $properties Associative array of properties to inject
     */
    public function __construct(array $properties = [])
    {
        // Hydrate properties dynamically
        foreach ($properties as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        // Initialize dependencies
        $this->builder = new Builder();
        $this->formatter = new Formatter(get_object_vars($this));
        $this->parser = new Parser();
    }

    /**
     * Render the Table Component.
     *
     * @param   array $serialized Data from the model
     * @param   int   $length     Length of data
     * @return  array Returns the processed table configuration
     */
    public function render(array $serialized = [], int $length = 0): array
    {
        if (empty($serialized)) {
            return [];
        }

        $request = Services::request();

        // Retrieve query string & clean token
        $query_params = $request->getGet();
        unset($query_params['aksara']);

        $output = [];
        $columns = [];
        $search_columns_rendered = false;

        // Default search column option
        $search_columns = [
            [
                'id' => null,
                'label' => phrase('All columns')
            ]
        ];

        // Loop through data rows
        foreach ($serialized as $key => $val) {
            // 1. Reorder Columns
            if (! empty($this->_column_order)) {
                $column_order = [];
                foreach ($this->_column_order as $order_val) {
                    if (array_key_exists($order_val, $val)) {
                        $column_order[] = $order_val;
                    }
                }
                $val = array_replace(array_flip($column_order), $val);
            }

            $field_data = [];
            $primary_key = [];
            $replacement = [];

            // 2. Extract replacements for Twig parsing later
            foreach ($val as $_field => $_params) {
                $replacement[$_field] = $_params['value'];
            }

            // 3. Process Each Field
            foreach ($val as $field => $params) {
                // Extract params
                $type = $params['type'];
                $primary = $params['primary'];
                $value = $params['value'];
                $content = $params['content'];
                $hidden = $params['hidden'];

                $label = ucwords(str_replace('_', ' ', $field));

                // Collection unique fields
                if (! in_array($field, $this->fields)) {
                    $this->fields[] = $field;
                }

                // Store primary key
                if ($primary) {
                    $primary_key[$field] = $value;
                }

                // Skip hidden fields (Logic: Hide if hidden=true, UNLESS it's primary key AND we are in API mode)
                // If API client is active, we might need primary key even if hidden.
                if ($hidden && (! $this->api_client || ($this->api_client && ! $primary))) {
                    continue;
                }

                // Label Overrides
                if (isset($this->_merge_label[$field])) {
                    $label = $this->_merge_label[$field];
                } elseif (isset($this->_set_alias[$field])) {
                    $label = $this->_set_alias[$field];
                }

                // Determine Field Type
                $field_type = array_intersect(array_keys($type), self::VALID_TYPES);
                if (empty($field_type)) {
                    $field_type = ['text'];
                }
                if (count($field_type) > 1) {
                    array_pop($field_type);
                }
                $final_type = end($field_type);

                // Build Table Column Header (Run once per field)
                if (! isset($columns[$field])) {
                    $columns[$field] = [
                        'field' => $field,
                        'label' => $label,
                        'url' => go_to(null, array_merge($query_params, ['order' => $field, 'sort' => get_userdata('sortOrder')])),
                        'icon' => 'mdi mdi-sort-' . ('asc' == get_userdata('sortOrder') ? 'ascending' : 'descending'),
                        'align' => (array_intersect(['int', 'integer', 'numeric', 'number_format', 'money', 'percent'], $field_type) ? 'right' : 'left')
                    ];
                }

                // Build Searchable Columns List (Run once)
                if (! $search_columns_rendered) {
                    $search_columns[] = [
                        'id' => $field,
                        'label' => $label,
                        'selected' => $request->getGet('column') === $field
                    ];
                }

                // If no data length (empty result set structure), skip processing content
                if (! $length) {
                    continue;
                }

                // --- Content Formatting ---

                // Masking
                if (array_intersect(['password', 'encryption'], array_keys($type))) {
                    $value = '*****';
                    $content = '*****';
                }

                // Merged Content Logic
                if (isset($this->_merge_content[$field])) {
                    if ($this->_merge_content[$field]['callback']) {
                        $router = Services::router();

                        // Callback execution
                        $namespace = $router->controllerName();
                        $class = new $namespace();
                        $callback = $this->_merge_content[$field]['callback'];

                        if (method_exists($class, $callback)) {
                            $content = $class->$callback($replacement);
                        }
                    } else {
                        // Parser execution
                        $content = $this->parser->parse($this->_merge_content[$field]['parameter'], $replacement);
                    }
                    $value = $content;
                }

                // Formatter execution
                $content = $this->formatter->format($field, $content, $type, $replacement);

                // Field Data Construction
                $field_data[$field] = [
                    'name' => $field,
                    'label' => $label,
                    'value' => $value,
                    'content' => $content,
                    'type' => $final_type,
                    'primary' => $primary,
                    'hidden' => $hidden,
                    'escape' => ! isset($this->_merge_content[$field])
                ];

                // Special handling for specific types
                if (in_array($final_type, ['image', 'images', 'carousel'])) {
                    $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
                } elseif ('hyperlink' === $final_type && isset($type['hyperlink']['beta'])) {
                    $field_data[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
                } elseif (in_array($final_type, ['text', 'mediumtext', 'longtext', 'textarea', 'wysiwyg']) && ! in_array($field, $this->_unset_truncate) && ! isset($this->_merge_content[$field])) {
                    $field_data[$field]['content'] = truncate($field_data[$field]['content'], 64);
                }

                // Parse Twig in content if exists
                if (is_string($field_data[$field]['content']) && strpos($field_data[$field]['content'], '{{') !== false) {
                    $field_data[$field]['content'] = $this->parser->parse($field_data[$field]['content'], $replacement);
                }

                // Grid View URL Logic
                if ($this->_grid_view && isset($this->_grid_view['hyperlink']) && strpos($this->_grid_view['hyperlink'], '://') === false && isset($this->_grid_view['parameter']) && ! isset($this->_grid_view['url'][$key])) {
                    $grid_query = [];
                    foreach ($this->_grid_view['parameter'] as $_key => $_val) {
                        $grid_query[$_key] = $val[$_val]['value'] ?? $_val;
                    }
                    $this->_grid_view['url'][$key] = base_url($this->_grid_view['hyperlink'], $grid_query);
                }

                // Scaffolding: Create component template if missing
                if (! $request->isAJAX() && ! $this->api_client && $final_type) {
                    $this->builder->get_component($this->_set_theme, 'table', $final_type);
                }
            }

            $search_columns_rendered = true;

            if (! $length) {
                break;
            }

            // Determine Deletion Permission for this row
            $deleting = ! in_array('delete', $this->_unset_method);
            foreach ($primary_key as $field => $value) {
                if (isset($this->_unset_delete[$field]) && in_array($value, $this->_unset_delete[$field])) {
                    $deleting = false;
                }
            }

            $output[] = [
                'primary' => $primary_key,
                'buttons' => $this->_get_buttons($primary_key, $replacement),
                'dropdowns' => $this->_get_dropdowns($primary_key, $replacement),
                'field_data' => $field_data,
                'deleting' => $deleting
            ];
        }

        // --- Toolbar Buttons Construction ---
        $buttons = $this->_build_toolbar_buttons($query_params);

        // --- Filters Construction ---
        $filters = [
            'q' => [
                'type' => 'text',
                'label' => phrase('Keywords'),
                'values' => $request->getGet('q')
            ],
            'column' => [
                'type' => 'select',
                'label' => phrase('Column'),
                'values' => $search_columns,
            ]
        ];

        if ($this->_add_filter) {
            $filters = array_merge($this->_add_filter, $filters);
        }

        // Final Output Structure
        $final_output = [
            'columns' => $columns,
            'table_data' => $output,
            'item_reference' => $this->_item_reference,
            'query_params' => $query_params,
            'toolbar' => [
                'action' => current_page(null, ['per_page' => null]),
                'buttons' => $buttons,
                'filters' => $filters
            ],
            'sortable' => $this->_sortable
        ];

        if ($this->_grid_view) {
            $final_output['grid'] = $this->_grid_view;
            $final_output['grid']['path'] = $this->_set_upload_path;
        }

        return $final_output;
    }

    /**
     * Build top toolbar buttons (Create, Export, Print, etc.)
     */
    private function _build_toolbar_buttons(array $query_params): array
    {
        $buttons = [];

        if (! in_array('create', $this->_unset_method)) {
            $buttons[] = $this->_set_link('create', phrase('Create'), 'btn-primary --modal', 'mdi mdi-plus', $query_params);
        }

        if ($this->_add_toolbar) {
            foreach ($this->_add_toolbar as $val) {
                $buttons[] = $this->_set_link($val['url'], $val['label'], $val['class'], $val['icon'], $val['parameter'], $val['new_tab']);
            }
        }

        $request = Services::request();
        $agent = $request->getUserAgent();

        if (! $agent->isMobile()) {
            $export_params = array_merge($query_params, ['keep_query' => true]);

            if (! in_array('read', $this->_unset_method)) {
                if (! in_array('export', $this->_unset_method)) {
                    $buttons[] = $this->_set_link('export', phrase('Export'), 'btn-success', 'mdi mdi-file-excel', $export_params, true);
                }
                if (! in_array('print', $this->_unset_method)) {
                    $buttons[] = $this->_set_link('print', phrase('Print'), 'btn-warning', 'mdi mdi-printer', $export_params, true);
                }
                if (! in_array('pdf', $this->_unset_method)) {
                    $buttons[] = $this->_set_link('pdf', phrase('PDF'), 'btn-info', 'mdi mdi-file-pdf', $export_params, true);
                }
            }

            if (! in_array('delete', $this->_unset_method)) {
                $buttons[] = $this->_set_link('delete', phrase('Batch Delete'), 'btn-danger d-none disabled --open-delete-confirm', 'mdi mdi-delete', $query_params);
            }
        } else {
            // Mobile Specific Buttons
            $buttons[] = $this->_set_link(null, phrase('Search'), 'btn-dark', 'mdi mdi-magnify', $query_params, false, 'data-bs-toggle="modal" data-bs-target="#searchModal"');
            $buttons[] = $this->_set_link(null, phrase('Refresh'), 'btn-secondary --xhr', 'mdi mdi-refresh', $query_params);
        }

        // Apply Button Overrides
        foreach ($buttons as $key => $val) {
            if (isset($this->_set_button[$val['path']])) {
                $override = $this->_set_button[$val['path']];
                $buttons[$key] = $this->_set_link(
                    $val['path'],
                    $override['label'],
                    $override['class'],
                    $override['icon'],
                    $override['parameter'],
                    $override['new_tab']
                );
            }
        }

        return $buttons;
    }

    /**
     * Get Row Action Buttons (Read, Update, Delete, Custom).
     */
    private function _get_buttons(array $query_params = [], array $replacement = []): array
    {
        $buttons = [];

        if (! in_array('read', $this->_unset_method)) {
            $buttons[] = [
                'url' => 'read',
                'label' => phrase('Read'),
                'class' => 'btn-primary --modal',
                'icon' => 'mdi mdi-magnify',
                'parameter' => $query_params,
                'new_tab' => false
            ];
        }

        if (! in_array('update', $this->_unset_method)) {
            $buttons[] = [
                'url' => 'update',
                'label' => phrase('Update'),
                'class' => 'btn-info --modal',
                'icon' => 'mdi mdi-square-edit-outline',
                'parameter' => $query_params,
                'new_tab' => false
            ];
        }

        if ($this->_add_button) {
            array_splice($buttons, 2, 0, $this->_add_button);
        }

        if (! in_array('delete', $this->_unset_method)) {
            $buttons[] = [
                'url' => 'delete',
                'label' => phrase('Delete'),
                'class' => 'btn-danger --open-delete-confirm',
                'icon' => 'mdi mdi-delete',
                'parameter' => $query_params,
                'new_tab' => false
            ];
        }

        return $this->_process_action_links($buttons, $replacement);
    }

    /**
     * Get Row Dropdown Actions.
     */
    private function _get_dropdowns(array $query_params = [], array $replacement = []): array
    {
        $dropdowns = [];

        if (! in_array('read', $this->_unset_method)) {
            if (! in_array('print', $this->_unset_method)) {
                $dropdowns[] = [
                    'url' => 'print',
                    'label' => phrase('Print'),
                    'class' => '',
                    'icon' => 'mdi mdi-printer',
                    'parameter' => $query_params,
                    'new_tab' => true
                ];
            }
            if (! in_array('pdf', $this->_unset_method)) {
                $dropdowns[] = [
                    'url' => 'pdf',
                    'label' => phrase('PDF'),
                    'class' => '',
                    'icon' => 'mdi mdi-file-pdf',
                    'parameter' => $query_params,
                    'new_tab' => true
                ];
            }
        }

        if ($this->_add_dropdown) {
            $dropdowns = array_merge($this->_add_dropdown, $dropdowns);
        }

        return $this->_process_action_links($dropdowns, $replacement);
    }

    /**
     * Process list of links/buttons, replace parameters, and apply overrides.
     */
    private function _process_action_links(array $links, array $replacement): array
    {
        foreach ($links as $key => $val) {
            // Apply Override
            if (isset($this->_set_button[$val['url']])) {
                $val = $this->_set_button[$val['url']];
            }

            // Parameter Replacement
            if (! empty($val['parameter'])) {
                foreach ($val['parameter'] as $_key => $_val) {
                    if (isset($replacement[$_val]) || in_array($_val, $this->fields)) {
                        $val['parameter'][$_key] = $replacement[$_val] ?? null;
                    }
                }
            }

            // Generate Link
            $links[$key] = $this->_set_link(
                $val['url'],
                $val['label'],
                $val['class'],
                $val['icon'],
                $val['parameter'],
                $val['new_tab'],
                $val['attribution'] ?? null
            );

            if (! $links[$key]) {
                unset($links[$key]);
            }
        }

        return $links;
    }

    /**
     * Helper to generate a standardized link array or null if restricted.
     */
    private function _set_link(?string $path, ?string $label, ?string $class, ?string $icon, array $query_params = [], bool $new_tab = false, ?string $attribution = null): ?array
    {
        // Check Restrictions (Unset Read/Update/Delete based on specific conditions)
        foreach ($query_params as $keyword => $value) {
            if (in_array($path, $this->_unset_method)) {
                return null;
            } elseif ('read' == $path && isset($this->_unset_read[$keyword]) && in_array($value, $this->_unset_read[$keyword])) {
                return null;
            } elseif ('update' == $path && isset($this->_unset_update[$keyword]) && in_array($value, $this->_unset_update[$keyword])) {
                return null;
            } elseif ('delete' == $path && isset($this->_unset_delete[$keyword]) && in_array($value, $this->_unset_delete[$keyword])) {
                return null;
            }
        }

        // URL Generation
        if (! $path || strpos($path, '://') === false) {
            $url = go_to($path, $query_params);
        } else {
            $url = $path;
            $new_tab = true;
        }

        return [
            'url' => $url,
            'path' => $path,
            'label' => $label,
            'class' => $class,
            'icon' => $icon,
            'new_tab' => $new_tab,
            'attribution' => $attribution
        ];
    }
}
