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

use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Builder\Builder;
use Aksara\Laboratory\Renderer\Formatter;
use Aksara\Laboratory\Renderer\Parser;

class Table
{
    /**
     * Load trait, get dynamic properties
     */
    use Traits;

    private $builder;
    private $formatter;
    private $parser;
    private $path;
    private $model;
    private $api_client;

    public function __construct($properties = [])
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }

        $this->builder = new Builder();
        $this->formatter = new Formatter(get_object_vars($this));
        $this->parser = new Parser($this->_set_theme);
    }

    public function render(array $serialized = [], int $length = 0)
    {
        if (! $serialized) {
            return [];
        }

        // Retrieve query string
        $query_string = service('request')->getGet();

        // Unset old token
        unset($query_string['aksara']);

        $output = [];
        $columns = [];
        $search_columns_rendered = false;
        $search_columns = [
            [
                'id' => null,
                'label' => phrase('All columns')
            ]
        ];

        foreach ($serialized as $key => $val) {
            if (is_array($this->_column_order) && sizeof($this->_column_order) > 0) {
                // Apply for column order
                $column_order = [];

                foreach ($this->_column_order as $order_key => $order_val) {
                    if (array_key_exists($order_val, $val)) {
                        $column_order[] = $order_val;
                    }
                }

                // Flip data to match column order
                $val = array_replace(array_flip($column_order), $val);
            }

            $field_data = [];
            $primary_key = [];
            $replacement = [];

            foreach ($val as $field => $params) {
                $type = $params['type'];
                $primary = $params['primary'];
                $value = $params['value'];
                $content = $params['content'];
                $maxlength = $params['maxlength'];
                $hidden = $params['hidden'];
                $validation = $params['validation'];
                $required = in_array('required', $validation);

                $label = ucwords(str_replace('_', ' ', $field));
                $placeholder = (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : null);
                $class = null;
                $readonly = null;

                // Store primary key as a token
                if ($primary) {
                    $primary_key[$field] = $value;
                }

                if ($hidden && (! $this->api_client || ($this->api_client && ! $primary))) {
                    // Skip showing field
                    continue;
                }

                if (isset($this->_merge_label[$field])) {
                    // Use from merge_label()
                    $label = $this->_merge_label[$field];
                } elseif (isset($this->_set_alias[$field])) {
                    // Use from set_alias()
                    $label = $this->_set_alias[$field];
                }

                // Valid field type definition
                $valid_type = [
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

                // Get field intersection
                $field_type = array_intersect(array_keys($type), $valid_type);

                if (! $field_type) {
                    // Fallback default input type
                    $field_type = ['text'];
                }

                if (sizeof($field_type) > 1) {
                    // Remove last element of array
                    array_pop($field_type);
                }

                // Push field into sortable column
                $columns[$field] = [
                    'field' => $field,
                    'label' => $label,
                    'url' => go_to(null, array_merge($query_string, ['order' => $field, 'sort' => get_userdata('sortOrder')])),
                    'icon' => 'mdi mdi-sort-' . ('asc' == get_userdata('sortOrder') ? 'ascending' : 'descending'),
                    'align' => (array_intersect(['int', 'integer', 'numeric', 'number_format', 'money', 'percent'], $field_type) ? 'right' : 'left')
                ];

                if (! $search_columns_rendered) {
                    // Push field into searchable column
                    $search_columns[] = [
                        'id' => $field,
                        'label' => $label,
                        'selected' => service('request')->getGet('column') === $field
                    ];
                }

                // Empty result, continue job
                if (! $length) {
                    continue;
                }

                // Getting twig replacer
                foreach ($val as $_field => $_params) {
                    // Pair replacement
                    $replacement[$_field] = $_params['value'];
                }

                // Get formatted content
                if (array_intersect(['password', 'encryption'], array_keys($type))) {
                    $value = '*****';
                    $content = '*****';
                }

                if (isset($this->_merge_content[$field])) {
                    if ($this->_merge_content[$field]['callback']) {
                        // Get formatted content of merged field (with callback)
                        $namespace = service('router')->controllerName();
                        $class = new $namespace();
                        $callback = $this->_merge_content[$field]['callback'];

                        if (method_exists($class, $callback)) {
                            // Get callback method of current controller
                            $content = $class->$callback($replacement);
                        }
                    } else {
                        $content = $this->parser->parse($this->_merge_content[$field]['parameter'], $replacement);
                    }

                    $value = $content;
                }

                // Get formatted content
                $content = $this->formatter->format($field, $content, $type, $replacement);


                // Field preparation
                $field_data[$field] = [
                    'name' => $field,
                    'type' => end($field_type),
                    'primary' => $primary,
                    'label' => $label,
                    'value' => $value,
                    'content' => $content,
                    'hidden' => $hidden,
                    'escape' => ! isset($this->_merge_content[$field])
                ];

                if (array_intersect($field_type, ['image', 'images', 'carousel'])) {
                    // Image type field
                    $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
                } elseif (array_intersect($field_type, ['hyperlink']) && isset($type['hyperlink']['beta'])) {
                    // File type hyperkink
                    $field_data[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
                } elseif (array_intersect($field_type, ['text', 'mediumtext', 'longtext', 'textarea', 'wysiwyg']) && ! in_array($field, $this->_unset_truncate) && ! isset($this->_merge_content[$field])) {
                    // File type hyperkink
                    $field_data[$field]['content'] = truncate($field_data[$field]['content'], 64);
                }

                // Find and replace Twig formatted content
                if (is_string($field_data[$field]['content']) && strpos($field_data[$field]['content'], '{{') !== false && strpos($field_data[$field]['content'], '}}')) {
                    // Replace content
                    $field_data[$field]['content'] = $this->parser->parse($field_data[$field]['content'], $replacement);
                }

                if ($this->_grid_view && $this->_grid_view['hyperlink'] && ($this->_grid_view['hyperlink'] && (stripos($this->_grid_view['hyperlink'], '://') === false)) && $this->_grid_view['parameter'] && ! isset($this->_grid_view['url'][$key])) {
                    $grid_query = [];

                    foreach ($this->_grid_view['parameter'] as $_key => $_val) {
                        $grid_query[$_key] = (isset($val[$_val]['value']) ? $val[$_val]['value'] : $_val);
                    }

                    $this->_grid_view['url'][$key] = base_url($this->_grid_view['hyperlink'], $grid_query);
                }

                // Parse content if request is made through non-promise request
                if (! service('request')->isAJAX() && ! $this->api_client && end($field_type)) {
                    // Get or create component of matches last field type element
                    $component = $this->builder->get_component($this->_set_theme, 'table', end($field_type));
                }
            }

            // Skip add search column on next rows
            $search_columns_rendered = true;

            // Empty result, break operation
            if (! $length) {
                break;
            }

            // Default deletion permission
            $deleting = (! in_array('delete', $this->_unset_method) ? true : false);

            // Getting deletion permission
            foreach ($primary_key as $field => $value) {
                if (isset($this->_unset_delete[$field]) && in_array($value, $this->_unset_delete[$field])) {
                    // Deletion is restricted
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

        $buttons = [];

        if (! in_array('create', $this->_unset_method)) {
            // Add create button toolbar
            $buttons[] = $this->_set_link('create', phrase('Create'), 'btn-primary --modal', 'mdi mdi-plus', $query_string);
        }

        if ($this->_add_toolbar) {
            foreach ($this->_add_toolbar as $key => $val) {
                // Add custom button toolbar
                $buttons[] = $this->_set_link($val['url'], $val['label'], $val['class'], $val['icon'], $val['parameter'], $val['new_tab']);
            }
        }

        // Get user agent
        $agent = service('request')->getUserAgent();

        if (! $agent->isMobile()) {
            if (! in_array('read', $this->_unset_method) && ! in_array('export', $this->_unset_method)) {
                $query_string['keep_query'] = true;

                // Add export button toolbar
                $buttons[] = $this->_set_link('export', phrase('Export'), 'btn-success', 'mdi mdi-file-excel', $query_string, true);
            }

            if (! in_array('read', $this->_unset_method) && ! in_array('print', $this->_unset_method)) {
                $query_string['keep_query'] = true;

                // Add print button toolbar
                $buttons[] = $this->_set_link('print', phrase('Print'), 'btn-warning', 'mdi mdi-printer', $query_string, true);
            }

            if (! in_array('read', $this->_unset_method) && ! in_array('pdf', $this->_unset_method)) {
                $query_string['keep_query'] = true;

                // Add PDF button toolbar
                $buttons[] = $this->_set_link('pdf', phrase('PDF'), 'btn-info', 'mdi mdi-file-pdf', $query_string, true);
            }

            if (! in_array('delete', $this->_unset_method)) {
                // Add PDF button toolbar
                $buttons[] = $this->_set_link('delete', phrase('Batch Delete'), 'btn-danger d-none disabled --open-delete-confirm', 'mdi mdi-delete', $query_string);
            }
        } else {
            // Add search button toolbar
            $buttons[] = $this->_set_link(null, phrase('Search'), 'btn-dark', 'mdi mdi-magnify', $query_string, false, 'data-bs-toggle="modal" data-bs-target="#searchModal"');

            // Add refresh button toolbar
            $buttons[] = $this->_set_link(null, phrase('Refresh'), 'btn-secondary --xhr', 'mdi mdi-refresh', $query_string);
        }

        // Default filters
        $filters = [
            'q' => [
                'type' => 'text',
                'label' => phrase('Keywords'),
                'values' => service('request')->getGet('q')
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

        $output = [
            'columns' => $columns,
            'table_data' => $output,
            'item_reference' => $this->_item_reference,
            'query_string' => $query_string,
            'toolbar' => [
                'action' => current_page(null, ['per_page' => null]),
                'buttons' => $buttons,
                'filters' => $filters
            ]
        ];

        if ($this->_grid_view) {
            $output['grid'] = $this->_grid_view;
            $output['grid']['path'] = $this->_set_upload_path;
        }

        return $output;
    }

    private function _get_buttons(array $query_string = [], array $replacement = [])
    {
        $buttons = [];

        if (! in_array('read', $this->_unset_method)) {
            $buttons[] = [
                'url' => 'read',
                'label' => phrase('Read'),
                'class' => 'btn-primary --modal',
                'icon' => 'mdi mdi-magnify',
                'parameter' => $query_string,
                'new_tab' => false
            ];
        }

        if (! in_array('update', $this->_unset_method)) {
            $buttons[] = [
                'url' => 'update',
                'label' => phrase('Update'),
                'class' => 'btn-info --modal',
                'icon' => 'mdi mdi-square-edit-outline',
                'parameter' => $query_string,
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
                'parameter' => $query_string,
                'new_tab' => false
            ];
        }

        foreach ($buttons as $key => $val) {
            if ($val['parameter']) {
                // Replace matches query string value
                foreach ($val['parameter'] as $_key => $_val) {
                    if (isset($replacement[$_val])) {
                        // Do replace
                        $val['parameter'][$_key] = $replacement[$_val];
                    }
                }
            }

            $buttons[$key] = $this->_set_link($val['url'], $val['label'], $val['class'], $val['icon'], $val['parameter'], $val['new_tab'], (isset($val['attribution']) ? $val['attribution'] : null));

            if (! $buttons[$key]) {
                unset($buttons[$key]);
            }
        }

        return $buttons;
    }

    private function _get_dropdowns(array $query_string = [], array $replacement = [])
    {
        $dropdowns = [];

        if (! in_array('read', $this->_unset_method) && ! in_array('print', $this->_unset_method)) {
            $dropdowns[] = [
                'url' => 'print',
                'label' => phrase('Print'),
                'class' => '',
                'icon' => 'mdi mdi-printer',
                'parameter' => $query_string,
                'new_tab' => true
            ];
        }

        if (! in_array('read', $this->_unset_method) && ! in_array('pdf', $this->_unset_method)) {
            $dropdowns[] = [
                'url' => 'pdf',
                'label' => phrase('PDF'),
                'class' => '',
                'icon' => 'mdi mdi-file-pdf',
                'parameter' => $query_string,
                'new_tab' => true
            ];
        }

        if ($this->_add_dropdown) {
            $dropdowns = array_merge($this->_add_dropdown, $dropdowns);
        }

        foreach ($dropdowns as $key => $val) {
            $val['parameter'] = array_replace($val['parameter'], array_intersect($val['parameter'], $replacement));

            $dropdowns[$key] = $this->_set_link($val['url'], $val['label'], $val['class'], $val['icon'], $val['parameter'], $val['new_tab'], (isset($val['attribution']) ? $val['attribution'] : null));

            if (! $dropdowns[$key]) {
                unset($dropdowns[$key]);
            }
        }

        return $dropdowns;
    }

    private function _set_link($path, $label, $class, $icon, $query_string = [], bool $new_tab = false, $attribution = null)
    {
        foreach ($query_string as $keyword => $value) {
            if (in_array($path, $this->_unset_method)) {
                return [];
            } elseif ('read' == $path && isset($this->_unset_read[$keyword]) && in_array($value, $this->_unset_read[$keyword])) {
                return [];
            } elseif ('update' == $path && isset($this->_unset_update[$keyword]) && in_array($value, $this->_unset_update[$keyword])) {
                return [];
            } elseif ('delete' == $path && isset($this->_unset_delete[$keyword]) && in_array($value, $this->_unset_delete[$keyword])) {
                return [];
            }
        }

        foreach (service('request')->getGet() as $key => $val) {
            // Slice non primary query string
            if (in_array($key, ['per_page', 'q', 'limit', 'offset', 'order', 'sort']) && ! isset($query_string['keep_query'])) {
                //$query_string[$key] = null;
            }
        }

        if (! $path || strpos($path, '://') === false) {
            $url = go_to($path, $query_string);
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
