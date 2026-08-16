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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
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
     * API Client Instance
     */
    private mixed $apiClient = null;

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
        'hyperlink', 'attribution', 'geospatial',
        'custom'
    ];

    /**
     * Constructor
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
     */
    public function render(array $serialized = [], int $length = 0): array
    {
        if (empty($serialized)) {
            return [];
        }

        $request = Services::request();

        // Retrieve query string & clean token
        $queryParams = $request->getGet();
        unset($queryParams['aksara']);

        $output = [];
        $columns = [];
        $searchColumnsRendered = false;

        // Default search column option
        $searchColumns = [
            [
                'id' => null,
                'label' => phrase('All columns')
            ]
        ];
        $callbackInstances = [];
        $componentChecked = [];
        $baseLabels = [];
        $knownFields = array_fill_keys($this->fields, true);
        $unsetColumns = array_fill_keys($this->_unsetColumn, true);
        $unsetTruncate = array_fill_keys($this->_unsetTruncate, true);

        // Loop through data rows
        foreach ($serialized as $key => $val) {
            // 1. Reorder Columns
            if (! empty($this->_columnOrder)) {
                $columnOrder = [];
                foreach ($this->_columnOrder as $orderVal) {
                    if (array_key_exists($orderVal, $val)) {
                        $columnOrder[] = $orderVal;
                    }
                }
                $val = array_replace(array_flip($columnOrder), $val);
            }

            $fieldData = [];
            $primaryKey = [];
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

                if (! isset($baseLabels[$field])) {
                    $baseLabels[$field] = ucwords(str_replace('_', ' ', $field));
                }

                $label = $baseLabels[$field];

                // Collection unique fields
                if (! isset($knownFields[$field])) {
                    $this->fields[] = $field;
                    $knownFields[$field] = true;
                }

                // Store primary key
                if ($primary) {
                    $primaryKey[$field] = $value;
                }

                // Skip hidden fields (Logic: Hide if hidden=true, UNLESS it's primary key AND we are in API mode)
                // If API client is active, we might need primary key even if hidden.
                if (($hidden || isset($unsetColumns[$field])) && (! $this->apiClient || ($this->apiClient && ! $primary))) {
                    continue;
                }

                // Label Overrides
                if (isset($this->_mergeLabel[$field])) {
                    $label = $this->_mergeLabel[$field];
                } elseif (isset($this->_setAlias[$field])) {
                    $label = $this->_setAlias[$field];
                }

                // Determine Field Type
                $fieldType = array_intersect(array_keys($type), self::VALID_TYPES);
                if (empty($fieldType)) {
                    $fieldType = ['text'];
                }
                if (count($fieldType) > 1) {
                    array_pop($fieldType);
                }
                $finalType = end($fieldType);

                // Build Table Column Header (Run once per field)
                if (! isset($columns[$field])) {
                    $columns[$field] = [
                        'field' => $field,
                        'label' => $label,
                        'url' => go_to(null, array_merge($queryParams, ['order' => $field, 'sort' => ('asc' == strtolower((string) get_userdata('sort_order')) ? 'desc' : 'asc')])),
                        'icon' => 'mdi mdi-sort-' . ('asc' == strtolower((string) get_userdata('sort_order')) ? 'ascending' : 'descending'),
                        'align' => (array_intersect(['int', 'integer', 'numeric', 'number_format', 'money', 'percent'], $fieldType) ? 'right' : 'left')
                    ];
                }

                // Build Searchable Columns List (Run once)
                if (! $searchColumnsRendered) {
                    $searchColumns[] = [
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

                $relationDisplayLabel = null;
                if (isset($this->_setRelation[$field])) {
                    $relationDisplayLabel = $content;
                }

                // Merged Content Logic
                if (isset($this->_mergeContent[$field])) {
                    if ($this->_mergeContent[$field]['callback']) {
                        $router = Services::router();

                        // Callback execution
                        $namespace = $router->controllerName();
                        if (! isset($callbackInstances[$namespace])) {
                            $callbackInstances[$namespace] = new $namespace();
                        }
                        $class = $callbackInstances[$namespace];
                        $callback = $this->_mergeContent[$field]['callback'];

                        if (method_exists($class, $callback)) {
                            $content = $class->$callback($replacement);
                        }
                    } else {
                        // Parser execution
                        $content = $this->parser->parse($this->_mergeContent[$field]['parameter'], $replacement);
                    }
                    $value = $content;
                }

                // Formatter execution
                $content = $this->formatter->format($field, $content, $type, $replacement);

                // Field Data Construction
                $fieldData[$field] = [
                    'name' => $field,
                    'label' => $label,
                    'value' => (isset($this->_setRelation[$field]) ? $relationDisplayLabel : $value),
                    'content' => $content,
                    'type' => $finalType,
                    'primary' => $primary,
                    'hidden' => $hidden,
                    'escape' => ! isset($this->_mergeContent[$field]),
                    'truncate' => ! isset($unsetTruncate[$field])
                ];

                if (in_array($finalType, ['range'])) {
                    $fieldData[$field]['attribution'] = $this->_setAttribute[$field] ?? null;
                }

                // Special handling for specific types
                if (in_array($finalType, ['image', 'images'])) {
                    $fieldData[$field]['placeholder'] = get_image($this->_setUploadPath, 'placeholder.png', 'thumb');
                } elseif ('hyperlink' === $finalType && isset($type['hyperlink']['beta'])) {
                    $fieldData[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
                } elseif (in_array($finalType, ['text', 'mediumtext', 'longtext', 'textarea', 'wysiwyg']) && ! isset($unsetTruncate[$field]) && ! isset($this->_mergeContent[$field])) {
                    $fieldData[$field]['content'] = truncate($fieldData[$field]['content'], 64);
                }

                // Parse Twig in content if exists
                if (is_string($fieldData[$field]['content']) && strpos($fieldData[$field]['content'], '{{') !== false) {
                    $fieldData[$field]['content'] = $this->parser->parse($fieldData[$field]['content'], $replacement);
                }

                // Grid View URL Logic
                if ($this->_gridView && isset($this->_gridView['hyperlink']) && strpos($this->_gridView['hyperlink'], '://') === false && isset($this->_gridView['parameter']) && ! isset($this->_gridView['url'][$key])) {
                    $gridQuery = [];
                    foreach ($this->_gridView['parameter'] as $_key => $_val) {
                        $gridQuery[$_key] = $val[$_val]['value'] ?? $_val;
                    }
                    $this->_gridView['url'][$key] = base_url($this->_gridView['hyperlink'], $gridQuery);
                }

                // Scaffolding: Create component template if missing
                if (! $request->isAJAX() && ! $this->apiClient && $finalType && ! isset($componentChecked[$finalType])) {
                    $this->builder->getComponent($this->_setTheme, 'table', $finalType);
                    $componentChecked[$finalType] = true;
                }
            }

            $searchColumnsRendered = true;

            if (! $length) {
                break;
            }

            // Determine Deletion Permission for this row
            $deleting = ! in_array('delete', $this->_unsetMethod);
            foreach ($primaryKey as $field => $value) {
                if (isset($this->_unsetDelete[$field]) && in_array($value, $this->_unsetDelete[$field])) {
                    $deleting = false;
                }
            }

            $output[] = [
                'primary' => $primaryKey,
                'buttons' => $this->_getButtons($primaryKey, $replacement),
                'dropdowns' => $this->_getDropdowns($primaryKey, $replacement),
                'field_data' => $fieldData,
                'deleting' => $deleting
            ];
        }

        // --- Toolbar Buttons Construction ---
        $buttons = $this->_buildToolbarButtons($queryParams);

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
                'values' => $searchColumns,
            ]
        ];

        if ($this->_addFilter) {
            $filters = array_merge($this->_addFilter, $filters);
        }

        // Final Output Structure
        $finalOutput = [
            'columns' => $columns,
            'table_data' => $output,
            'item_reference' => $this->_itemReference,
            'query_params' => $queryParams,
            'toolbar' => [
                'action' => current_page(null, ['page' => null]),
                'buttons' => $buttons,
                'filters' => $filters
            ],
            'sortable' => $this->_sortable
        ];

        if ($this->_gridView) {
            $finalOutput['grid'] = $this->_gridView;
            $finalOutput['grid']['path'] = $this->_setUploadPath;
        }

        return $finalOutput;
    }

    /**
     * Build top toolbar buttons (Create, Export, Print, etc.)
     */
    private function _buildToolbarButtons(array $queryParams): array
    {
        $buttons = [];

        if (! in_array('create', $this->_unsetMethod) && ! in_array('create', $this->_unsetToolbar)) {
            $buttons[] = $this->_setLink('create', phrase('Create'), 'btn-primary --modal', 'mdi mdi-plus', $queryParams);
        }

        if ($this->_addToolbar) {
            foreach ($this->_addToolbar as $val) {
                if (in_array($val['url'], $this->_unsetMethod) && in_array($val['url'], $this->_unsetToolbar)) {
                    continue;
                }

                $buttons[] = $this->_setLink($val['url'], $val['label'], $val['class'], $val['icon'], $val['parameter'], $val['new_tab']);
            }
        }

        $request = Services::request();
        $agent = $request->getUserAgent();

        if (! $agent->isMobile()) {
            $exportParams = array_merge($queryParams, ['keep_query' => true]);

            if (! in_array('read', $this->_unsetMethod)) {
                if (! in_array('export', $this->_unsetMethod) && ! in_array('export', $this->_unsetToolbar)) {
                    $buttons[] = $this->_setLink('export', phrase('Export'), 'btn-success', 'mdi mdi-file-excel', $exportParams, true);
                }
                if (! in_array('print', $this->_unsetMethod) && ! in_array('print', $this->_unsetToolbar)) {
                    $buttons[] = $this->_setLink('print', phrase('Print'), 'btn-warning', 'mdi mdi-printer', $exportParams, true);
                }
                if (! in_array('pdf', $this->_unsetMethod) && ! in_array('pdf', $this->_unsetToolbar)) {
                    $buttons[] = $this->_setLink('pdf', phrase('PDF'), 'btn-info', 'mdi mdi-file-pdf', $exportParams, true);
                }
            }

            if (! in_array('delete', $this->_unsetMethod) && ! in_array('delete', $this->_unsetToolbar)) {
                $buttons[] = $this->_setLink('delete', phrase('Batch Delete'), 'btn-danger d-none disabled --open-delete-confirm', 'mdi mdi-delete', $queryParams);
            }
        } else {
            // Mobile Specific Buttons
            $buttons[] = $this->_setLink(null, phrase('Search'), 'btn-dark', 'mdi mdi-magnify', $queryParams, false, 'data-bs-toggle="modal" data-bs-target="#searchModal"');
            $buttons[] = $this->_setLink(null, phrase('Refresh'), 'btn-secondary --xhr', 'mdi mdi-refresh', $queryParams);
        }

        // Apply Button Overrides
        foreach ($buttons as $key => $val) {
            if (! $val) {
                unset($buttons[$key]);

                continue;
            }

            if (isset($this->_setButton[$val['path']])) {
                $override = $this->_setButton[$val['path']];
                $buttons[$key] = $this->_setLink(
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
    private function _getButtons(array $queryParams = [], array $replacement = []): array
    {
        $buttons = [];

        if (! in_array('read', $this->_unsetMethod)) {
            $buttons[] = [
                'url' => 'read',
                'label' => phrase('Read'),
                'class' => 'btn-primary --modal',
                'icon' => 'mdi mdi-magnify',
                'parameter' => $queryParams,
                'new_tab' => false
            ];
        }

        if (! in_array('update', $this->_unsetMethod)) {
            $buttons[] = [
                'url' => 'update',
                'label' => phrase('Update'),
                'class' => 'btn-info --modal',
                'icon' => 'mdi mdi-square-edit-outline',
                'parameter' => $queryParams,
                'new_tab' => false
            ];
        }

        if ($this->_addButton) {
            array_splice($buttons, 2, 0, $this->_addButton);
        }

        if (! in_array('delete', $this->_unsetMethod)) {
            $buttons[] = [
                'url' => 'delete',
                'label' => phrase('Delete'),
                'class' => 'btn-danger --open-delete-confirm',
                'icon' => 'mdi mdi-delete',
                'parameter' => $queryParams,
                'new_tab' => false
            ];
        }

        return $this->_processActionLinks($buttons, $replacement);
    }

    /**
     * Get Row Dropdown Actions.
     */
    private function _getDropdowns(array $queryParams = [], array $replacement = []): array
    {
        $dropdowns = [];

        if (! in_array('read', $this->_unsetMethod)) {
            if (! in_array('print', $this->_unsetMethod)) {
                $dropdowns[] = [
                    'url' => 'print',
                    'label' => phrase('Print'),
                    'class' => '',
                    'icon' => 'mdi mdi-printer',
                    'parameter' => $queryParams,
                    'new_tab' => true
                ];
            }
            if (! in_array('pdf', $this->_unsetMethod)) {
                $dropdowns[] = [
                    'url' => 'pdf',
                    'label' => phrase('PDF'),
                    'class' => '',
                    'icon' => 'mdi mdi-file-pdf',
                    'parameter' => $queryParams,
                    'new_tab' => true
                ];
            }
        }

        if ($this->_addDropdown) {
            $dropdowns = array_merge($this->_addDropdown, $dropdowns);
        }

        return $this->_processActionLinks($dropdowns, $replacement);
    }

    /**
     * Process list of links/buttons, replace parameters, and apply overrides.
     */
    private function _processActionLinks(array $links, array $replacement): array
    {
        foreach ($links as $key => $val) {
            // Apply Override
            if (isset($this->_setButton[$val['url']])) {
                $val = $this->_setButton[$val['url']];
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
            $links[$key] = $this->_setLink(
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
    private function _setLink(?string $path, ?string $label, ?string $class, ?string $icon, array $queryParams = [], bool $newTab = false, ?string $attribution = null): ?array
    {
        if (! isset($queryParams['limit'])) {
            // Unset limit from query params
            $queryParams['limit'] = null;
        }
        if (! isset($queryParams['offset'])) {
            // Unset offset from query params
            $queryParams['offset'] = null;
        }
        if (! isset($queryParams['page'])) {
            // Unset page from query params
            $queryParams['page'] = null;
        }
        if (! isset($queryParams['order'])) {
            // Unset order from query params
            $queryParams['order'] = null;
        }
        if (! isset($queryParams['sort'])) {
            // Unset sort from query params
            $queryParams['sort'] = null;
        }
        if (! isset($queryParams['q'])) {
            // Unset search keyword from query params
            $queryParams['q'] = null;
        }

        // Check Restrictions (Unset Read/Update/Delete based on specific conditions)
        foreach ($queryParams as $keyword => $value) {
            if (in_array($path, $this->_unsetMethod)) {
                return null;
            } elseif ('read' == $path && isset($this->_unsetRead[$keyword]) && in_array($value, $this->_unsetRead[$keyword])) {
                return null;
            } elseif ('update' == $path && isset($this->_unsetUpdate[$keyword]) && in_array($value, $this->_unsetUpdate[$keyword])) {
                return null;
            } elseif ('delete' == $path && isset($this->_unsetDelete[$keyword]) && in_array($value, $this->_unsetDelete[$keyword])) {
                return null;
            }
        }

        // URL Generation
        if (! $path || strpos($path, '://') === false) {
            $url = go_to($path, $queryParams);
        } else {
            $url = $path;
            $newTab = true;
        }

        return [
            'url' => $url,
            'path' => $path,
            'label' => $label,
            'class' => $class,
            'icon' => $icon,
            'new_tab' => $newTab,
            'attribution' => $attribution
        ];
    }
}
