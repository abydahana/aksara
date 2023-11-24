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

class View
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

        $replacement = [];

        // Getting mustache replacer
        foreach ($serialized as $field => $params) {
            // Pair replacement
            $replacement[$field] = $params['value'];
        }

        // Loop serialized data
        foreach ($serialized as $field => $params) {
            $type = $params['type'];
            $primary = $params['primary'];
            $value = $params['value'];
            $content = $params['content'];
            $maxlength = $params['maxlength'];
            $hidden = $params['hidden'];
            $validation = explode('|', $params['validation']);
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

            // Valid field type definition
            $valid_type = [
                'text', 'textarea', 'wysiwyg',
                'number', 'decimal', 'currency', 'percent',
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
            } else {
                // Get formatted content
                $content = $this->formatter->format($content, $type, $replacement);
            }

            // Add to form data response
            $field_data[$field] = [
                'name' => $field,
                'type' => end($field_type),
                'primary' => $primary,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'tooltip' => (isset($this->_set_tooltip[$field]) ? $this->_set_tooltip[$field] : null),
                'position' => (isset($this->_field_position[$field]) ? $this->_field_position[$field] : 1),
                'prepend' => (isset($this->_field_prepend[$field]) ? $this->_field_prepend[$field] : null),
                'append' => (isset($this->_field_append[$field]) ? $this->_field_append[$field] : null),
                'merged' => in_array($field, $merged_fields),
                'escape' => ! isset($this->_merge_content[$field])
            ];

            if (array_intersect($field_type, ['image', 'images', 'carousel'])) {
                // Image type field
                $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
            } elseif (array_intersect($field_type, ['hyperlink']) && isset($type['hyperlink']['beta'])) {
                // File type hyperkink
                $field_data[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
            }

            // Find and replace Twig formatted content
            if (is_string($field_data[$field]['content']) && strpos($field_data[$field]['content'], '{{') !== false && strpos($field_data[$field]['content'], '}}')) {
                // Replace content
                $field_data[$field]['content'] = $this->parser->parse($field_data[$field]['content'], $replacement);
            }

            // Parse content if request is made through non-promise request
            if (! service('request')->isAJAX() && ! $this->api_client && end($field_type)) {
                // Get or create component of matches last field type element
                $component = $this->builder->get_component($this->_set_theme, 'view', end($field_type));
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
