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
 * View Component Renderer
 *
 * This class is responsible for rendering the Detail/Read view of a specific record.
 * It formats data for display, handles merging fields (e.g. combining First & Last Name),
 * and parses dynamic content using Twig/Mustache syntax.
 */
class View
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
     * Valid Field Types for View
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
     * Render the View Component.
     *
     * @param   array $serialized Data from the model
     * @param   int   $length     Length of data
     * @return  array Returns the processed view configuration
     */
    public function render(array $serialized = [], int $length = 0): array
    {
        if (empty($serialized)) {
            return [];
        }

        $request = Services::request();

        $primary_key = [];
        $field_data = [];
        $merged_fields = [];

        // Flatten merged fields
        if ($this->_merge_field) {
            foreach ($this->_merge_field as $val) {
                $merged_fields = array_merge($merged_fields, $val);
            }
        }

        // 1. Sort Fields
        $serialized = $this->_sort_fields($serialized);

        // 2. Prepare Replacements (for Mustache/Twig parsing)
        $replacement = [];
        foreach ($serialized as $field => $params) {
            $replacement[$field] = $params['value'];
        }

        // 3. Process Fields
        foreach ($serialized as $field => $params) {
            $type = $params['type'];
            $primary = $params['primary'];
            $value = $params['value'];
            $content = $params['content'];
            $hidden = $params['hidden'];

            $label = ucwords(str_replace('_', ' ', $field));

            // Store primary key
            if ($primary) {
                $primary_key[$field] = $value;
            }

            // Skip hidden fields
            if ($hidden) {
                continue;
            }

            // Label Overrides
            if (isset($this->_merge_label[$field])) {
                $label = $this->_merge_label[$field];
            } elseif (isset($this->_set_alias[$field])) {
                $label = $this->_set_alias[$field];
            }

            // Determine Input Type
            $field_type = $this->_get_input_type($type);
            $final_type = end($field_type);

            // Masking (Password/Encryption)
            if (array_intersect(['password', 'encryption'], array_keys($type))) {
                $value = '*****';
                $content = '*****';
            }

            // Handle Merged Content (Callbacks or Parsing)
            if (isset($this->_merge_content[$field])) {
                $content = $this->_merge_content($field, $replacement);
                $value = $content;
            }

            // Format Content
            $content = $this->formatter->format($field, $content, $type, $replacement);

            // Construct Field Data
            $field_data[$field] = [
                'name' => $field,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'type' => $final_type,
                'primary' => $primary,
                'tooltip' => $this->_set_tooltip[$field] ?? null,
                'position' => $this->_field_position[$field] ?? 1,
                'prepend' => $this->_field_prepend[$field] ?? null,
                'append' => $this->_field_append[$field] ?? null,
                'merged' => in_array($field, $merged_fields),
                'escape' => ! isset($this->_merge_content[$field])
            ];

            // Type-specific adjustments
            if (in_array($final_type, ['image', 'images', 'carousel'])) {
                $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
            } elseif ('hyperlink' === $final_type && isset($type['hyperlink']['beta'])) {
                $field_data[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
            }

            // Parse Twig within content (Double Parsing check)
            if (is_string($field_data[$field]['content']) && strpos($field_data[$field]['content'], '{{') !== false) {
                $field_data[$field]['content'] = $this->parser->parse($field_data[$field]['content'], $replacement);
            }

            // Scaffolding: Create template if missing
            if (! $request->isAJAX() && ! $this->api_client && $final_type) {
                $this->builder->get_component($this->_set_theme, 'view', $final_type);
            }
        }

        // 4. Final Output Preparation
        $highest_column = 1;
        if (! empty($this->_field_position)) {
            $highest_column = max($this->_field_position);
        }

        $query_params = array_replace($request->getGet(), $primary_key);

        if ($this->api_client) {
            unset($query_params['aksara'], $query_params['limit']);
        }

        return [
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
            'query_params' => $query_params
        ];
    }

    /**
     * Sort fields based on controller configuration.
     */
    private function _sort_fields(array $serialized): array
    {
        $order_source = [];

        if (! empty($this->_view_order)) {
            $order_source = $this->_view_order;
        } elseif (! empty($this->_field_order)) {
            $order_source = $this->_field_order;
        } elseif (! empty($this->_column_order)) {
            $order_source = $this->_column_order;
        }

        if (! empty($order_source)) {
            $sorted = [];
            foreach ($order_source as $val) {
                if (array_key_exists($val, $serialized)) {
                    $sorted[] = $val;
                }
            }
            return array_replace(array_flip($sorted), $serialized);
        }

        return $serialized;
    }

    /**
     * Determine valid input type from type definition.
     */
    private function _get_input_type(array $type): array
    {
        $field_type = array_intersect(array_keys($type), self::VALID_TYPES);

        if (empty($field_type)) {
            $field_type = ['text'];
        }

        if (count($field_type) > 1) {
            array_pop($field_type);
        }

        return $field_type;
    }

    /**
     * Process merge content using Callback or Parser.
     */
    private function _merge_content(string $field, array $replacement): mixed
    {
        $merge_config = $this->_merge_content[$field];

        if (! empty($merge_config['callback'])) {
            $router = Services::router();

            // Execute Controller Callback
            $namespace = $router->controllerName();
            $class = new $namespace();
            $callback = $merge_config['callback'];

            if (method_exists($class, $callback)) {
                return $class->$callback($replacement);
            }
        }

        // Execute String Parsing
        return $this->parser->parse($merge_config['parameter'], $replacement);
    }
}
