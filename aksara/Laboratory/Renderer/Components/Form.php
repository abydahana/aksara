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

class Form
{
    /**
     * Load trait, get dynamic properties
     */
    use Traits;

    private $builder;
    private $formatter;
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
    }

    public function render(array $serialized = [], int $length = 0)
    {
        if (! $serialized) {
            return [];
        }

        $primary_key = [];
        $field_data = [];
        $merged_fields = [];

        if ($this->_merge_field) {
            foreach ($this->_merge_field as $key => $val) {
                $merged_fields = array_merge($merged_fields, $val);
            }
        }

        $serialized = $serialized[0];

        if (is_array($this->_field_order) && sizeof($this->_field_order) > 0) {
            // Indicates field order
            $field_order = [];

            foreach ($this->_field_order as $order_key => $order_val) {
                if (array_key_exists($order_val, $serialized)) {
                    $field_order[] = $order_val;
                }
            }

            // Flip array and order by user expect
            $serialized = array_replace(array_flip($field_order), $serialized);
        } elseif (is_array($this->_column_order) && sizeof($this->_column_order) > 0) {
            // Backup order to follow the columns order
            $column_order = [];

            foreach ($this->_column_order as $order_key => $order_val) {
                if (array_key_exists($order_val, $serialized)) {
                    $column_order[] = $order_val;
                }
            }

            $serialized = array_replace(array_flip($column_order), $serialized);
        }

        // Loop serialized data
        foreach ($serialized as $field => $params) {
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

            if ($hidden || array_intersect(['current_timestamp'], array_keys($type))) {
                // Skip showing field
                continue;
            }

            if (isset($this->_merge_label[$field])) {
                // Use from merge_content()
                $label = $this->_merge_label[$field];
            } elseif (isset($this->_set_alias[$field])) {
                // Use alias from set_alias()
                $label = $this->_set_alias[$field];
            }

            if (in_array('slug', array_keys($type))) {
                // Override placeholder
                $placeholder = phrase('Leave blank to generate automatically');
            } elseif (in_array('boolean', array_keys($type))) {
                // Override placeholder
                $placeholder = phrase('Check to activate');
            } elseif (array_intersect(array_keys($type), ['select'])) {
                // Override placeholder
                $placeholder = phrase('Please choose');
            }

            if (in_array('readonly', array_keys($type))) {
                // Readonly
                $readonly = 'readonly';

                // Slice readonly field type
                unset($type['readonly']);
            } elseif (in_array('disabled', array_keys($type))) {
                // Disabled
                $readonly = 'disabled';

                // Slice readonly field type
                unset($type['disabled']);
            }

            // Valid field type definition
            $valid_type = [
                'text', 'textarea', 'wysiwyg',
                'number', 'money', 'percent',
                'select', 'checkbox', 'radio', 'boolean', 'range', 'color',
                'date', 'datetime', 'time', 'week', 'month',
                'hidden', 'email', 'password', 'encryption',
                'file', 'files', 'image', 'images',
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

            // Get formatted content
            if (array_intersect(['password', 'encryption'], array_keys($type))) {
                $value = '*****';
                $content = '*****';
            } elseif (array_intersect(['geospatial'], array_keys($type)) && ! sizeof(json_decode($value, true) ?? [])) {
                $value = get_setting('office_map');
                $content = $value;
            } else {
                $content = $this->formatter->format($content, $type);
            }

            $checked = $value || false;

            if ('create' === $this->_method) {
                if (array_intersect(['boolean'], array_keys($type))) {
                    $checked = true;
                } elseif (array_intersect(['select'], array_keys($type)) && is_array($content)) {
                    foreach ($content as $key => $val) {
                        // Match selected value
                        $content[$key]['selected'] = (isset($this->_default_value[$field]) && $this->_default_value[$field] == $val['value']) || false;
                    }
                } elseif (array_intersect(['checkbox', 'radio'], array_keys($type)) && is_array($content)) {
                    foreach ($content as $key => $val) {
                        // Match checked value
                        $content[$key]['checked'] = (isset($this->_default_value[$field]) && $this->_default_value[$field] == $val['value']) || false;
                    }
                } elseif (isset($this->_default_value[$field])) {
                    $value = $this->_default_value[$field];
                    $content = $value;
                }

                if (array_intersect(['last_insert'], array_keys($type))) {
                    if (! isset($this->_default_value[$field])) {
                        $last_insert_params = array_search('last_insert', $type);
                        $type_key = (isset($parameter[$last_insert_params]) && $parameter[$last_insert_params] ? array_search('{1}', explode('/', $parameter[$last_insert_params])) : null);
                        $where = [];

                        if ($this->_where) {
                            foreach ($this->_where as $key => $val) {
                                if ($this->model->field_exists($key, $this->_table)) {
                                    $where[$key] = $val;
                                }
                            }
                        }

                        if (in_array($this->_db_driver, ['SQLSRV'])) {
                            $cast_field = 'CONVERT(' . $field . ', SIGNED INTEGER)';
                        } else {
                            $cast_field = 'CAST(' . $field . ' AS SIGNED INTEGER)';
                        }

                        if (1 == 1) { // Skip
                            if (isset($parameter[$last_insert_params]) && is_array($parameter[$last_insert_params])) {
                                $this->model->where($parameter[$last_insert_params]);
                            }

                            $last_insert = $this->model->select((in_array($this->_db_driver, ['Postgre']) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $cast_field . '), 0) AS ' . $field)->order_by($field, 'desc')->get($this->_table, 1)->row($field);
                        } else {
                            $last_insert = $this->model->select((in_array($this->_db_driver, ['Postgre']) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $cast_field . '), 0) AS ' . $field)->order_by($field, 'desc')->get_where($this->_table, $where, 1)->row($field);
                        }

                        if ($last_insert) {
                            $last_insert = (strpos($last_insert, '/') !== false ? explode('/', $last_insert) : [$last_insert]);
                            $last_insert = (isset($last_insert[$type_key]) ? $last_insert[$type_key] : $last_insert[0]);
                            $last_insert = preg_replace('/[^0-9]/', '', $last_insert);
                        }

                        $last_insert = (! is_array($last_insert) && $last_insert > 0 ? $last_insert : 0) + 1;

                        $value = ($last_insert > 0 ? $last_insert : 1);

                        if (array_intersect(['sprintf'], array_keys($type))) {
                            $value = sprintf((isset($extra_params[$last_insert_params]) && is_string($extra_params[$last_insert_params]) ? $extra_params[$last_insert_params] : '%04d'), $value);
                        }

                        if (isset($parameter[$last_insert_params]) && $parameter[$last_insert_params]) {
                            $value = str_replace('{1}', $value, $parameter[$last_insert_params]);
                        }
                    }
                }
            } else {
                if (array_intersect(['select'], array_keys($type)) && is_array($content)) {
                    foreach ($content as $key => $val) {
                        // Match selected value
                        $content[$key]['selected'] = $value == $val['value'];
                    }
                } elseif (array_intersect(['checkbox', 'radio'], array_keys($type)) && is_array($content)) {
                    $has_checked = false;

                    foreach ($content as $key => $val) {
                        // Match checked value
                        $content[$key]['checked'] = $value == $val['value'];

                        if (! $has_checked && $value == $val['value']) {
                            // Checked found
                            $has_checked = true;
                        }
                    }

                    if (! $has_checked) {
                        // Backup default checked
                        foreach ($content as $key => $val) {
                            $content[$key]['checked'] = true;

                            break;
                        }
                    }
                }

                if (array_intersect(['sprintf'], array_keys($type))) {
                    $value = str_replace('{1}', sprintf((isset($extra_params[$last_insert_params]) && is_string($extra_params[$last_insert_params]) ? $extra_params[$last_insert_params] : '%04d'), $value), $parameter[$last_insert_params]);
                }
            }

            // Add to form data response
            $field_data[$field] = [
                'name' => $field,
                'type' => end($field_type),
                'primary' => $primary,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'maxlength' => $maxlength,
                'class' => (isset($this->_add_class[$field]) ? $this->_add_class[$field] : null),
                'placeholder' => $placeholder,
                'required' => $required,
                'checked' => $checked,
                'readonly' => $readonly,
                'accept' => gettype($value),
                'relation' => isset($this->_set_relation[$field]),
                'tooltip' => (isset($this->_set_tooltip[$field]) ? $this->_set_tooltip[$field] : null),
                'attribution' => (isset($this->_set_attribute[$field]) ? $this->_set_attribute[$field] : null),
                'position' => (isset($this->_field_position[$field]) ? $this->_field_position[$field] : 1),
                'prepend' => (isset($this->_field_prepend[$field]) ? $this->_field_prepend[$field] : null),
                'append' => (isset($this->_field_append[$field]) ? $this->_field_append[$field] : null),
                'merged' => in_array($field, $merged_fields)
            ];

            if (array_intersect($field_type, ['image', 'images', 'carousel'])) {
                // Image type field
                $field_data[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED))));
                $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
            } elseif (array_intersect($field_type, ['file', 'files'])) {
                // File type field
                $field_data[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED))));
            }

            // Parse content if request is made through non-promise request
            if (! service('request')->isAJAX() && ! $this->api_client && end($field_type)) {
                // Get or create component of matches last field type element
                $component = $this->builder->get_component($this->_set_theme, 'form', end($field_type));
            }
        }

        $column_size = 6;
        $column_offset = 3;
        $highest_column = 1;

        if (is_array($this->_field_position) && sizeof($this->_field_position) > 0) {
            $highest_column = max($this->_field_position);
        }

        $query_string = array_replace(service('request')->getGet(), $primary_key);

        if ($this->api_client) {
            unset($query_string['aksara'], $query_string['limit']);
        }

        $output = [
            'column_size' => $this->_column_size,
            'column_total' => $highest_column,
            'extra_action' => [
                'submit' => $this->_extra_submit
            ],
            'form_size' => ($this->_modal_size ? str_replace('modal', 'form', $this->_modal_size) : ''),
            'field_size' => $this->_field_size,
            'field_data' => $field_data,
            'merged_content' => $this->_merge_content,
            'merged_field' => $this->_merge_field,
            'set_heading' => $this->_set_heading,
            'grouped_field' => $this->_group_field,
            'query_string' => $query_string
        ];

        return $output;
    }
}
