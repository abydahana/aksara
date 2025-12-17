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

/**
 * Form Component Renderer
 *
 * This class handles the complex logic of transforming serialized database definition data
 * into a structured array ready for the Form Builder. It handles input type detection,
 * value formatting, auto-increment generation (custom), and field attributes.
 */
class Form
{
    /**
     * Load traits to access dynamic properties.
     */
    use Traits;

    /**
     * UI Builder Instance.
     */
    private Builder $builder;

    /**
     * Data Formatter Instance.
     */
    private Formatter $formatter;

    /**
     * Current module path.
     */
    private ?string $path = null;

    /**
     * Database Model Instance.
     */
    private mixed $model = null;

    /**
     * API Client Status/Instance.
     */
    private mixed $api_client = null;

    /**
     * Valid HTML5 and Custom Input Types supported by the builder.
     * @var array
     */
    private const VALID_TYPES = [
        'text', 'textarea', 'wysiwyg',
        'number', 'money', 'percent',
        'select', 'checkbox', 'radio', 'boolean', 'range', 'color',
        'date', 'datetime', 'time', 'week', 'month',
        'hidden', 'email', 'password', 'encryption',
        'file', 'files', 'image', 'images',
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
    }

    /**
     * Render the Form Data.
     *
     * Processes serialized data, determines input types, handles values,
     * and prepares the structure for the view.
     *
     * @param   array $serialized Data from the model
     * @param   int   $length     Length of data (unused in this context but kept for interface consistency)
     * @return  array Returns the processed form configuration
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

        // Flatten merged fields for easy lookup
        if ($this->_merge_field) {
            foreach ($this->_merge_field as $key => $val) {
                $merged_fields = array_merge($merged_fields, $val);
            }
        }

        // 1. Sort Fields based on configuration
        $serialized = $this->_sort_fields($serialized);

        // 2. Loop serialized data to process each field
        foreach ($serialized as $field => $params) {
            // Extract parameters
            $type = $params['type']; // Array of types/attributes
            $primary = $params['primary'];
            $value = $params['value'];
            $content = $params['content'];
            $maxlength = $params['maxlength'];
            $hidden = $params['hidden'];
            $validation = $params['validation'];
            $required = in_array('required', $validation);

            // Default label from field name
            $label = ucwords(str_replace('_', ' ', $field));
            $placeholder = $this->_set_placeholder[$field] ?? null;
            $class = $this->_add_class[$field] ?? null;
            $readonly = null;

            // Collect Primary Key
            if ($primary) {
                $primary_key[$field] = $value;
            }

            // Skip hidden fields or timestamps
            if ($hidden || array_intersect(['current_timestamp'], array_keys($type))) {
                continue;
            }

            // Override Labels
            if (isset($this->_merge_label[$field])) {
                $label = $this->_merge_label[$field];
            } elseif (isset($this->_set_alias[$field])) {
                $label = $this->_set_alias[$field];
            }

            // Handle Placeholders based on Type Key
            if (array_key_exists('slug', $type)) {
                $placeholder = phrase('Leave blank to generate automatically');
            } elseif (array_key_exists('boolean', $type)) {
                $placeholder = phrase('Check to activate');
            } elseif (array_key_exists('select', $type)) {
                $placeholder = phrase('Please choose');
            }

            // Handle Readonly/Disabled attributes
            if (array_key_exists('readonly', $type)) {
                $readonly = 'readonly';
                unset($type['readonly']);
            } elseif (array_key_exists('disabled', $type)) {
                $readonly = 'disabled';
                unset($type['disabled']);
            }

            // Determine Input Type
            $field_type = $this->_determine_input_type($type);
            $final_type = end($field_type); // Get the specific type (e.g., 'text')

            // Format Value & Content
            if (array_intersect(['password', 'encryption'], array_keys($type))) {
                $value = '*****';
                $content = '*****';
            } elseif (array_key_exists('geospatial', $type) && empty(json_decode($value ?? '', true))) {
                $value = get_setting('office_map');
                $content = $value;
            } else {
                $content = $this->formatter->format($field, $content, $type);
            }

            $checked = $value ? true : false;

            // --- Handle Logic based on Method (Create vs Update) ---
            if ('create' === $this->_method) {
                // CREATE MODE
                list($value, $checked) = $this->_handle_create_mode($field, $type, $value, $checked);
            } else {
                // UPDATE MODE
                list($value, $content) = $this->_handle_update_mode($type, $value, $content);
            }

            // Prepare Field Data Structure
            $field_data[$field] = [
                'name' => $field,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'type' => $final_type,
                'primary' => $primary,
                'maxlength' => $maxlength,
                'class' => $class,
                'placeholder' => $placeholder,
                'required' => $required,
                'checked' => $checked,
                'readonly' => $readonly,
                'accept' => gettype($value),
                'relation' => isset($this->_set_relation[$field]),
                'tooltip' => $this->_set_tooltip[$field] ?? null,
                'attribution' => $this->_set_attribute[$field] ?? null,
                'position' => $this->_field_position[$field] ?? 1,
                'prepend' => $this->_field_prepend[$field] ?? null,
                'append' => $this->_field_append[$field] ?? null,
                'merged' => in_array($field, $merged_fields)
            ];

            // Specific handling for File/Image inputs
            if (in_array($final_type, ['image', 'images', 'carousel'])) {
                $field_data[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED))));
                $field_data[$field]['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');
            } elseif (in_array($final_type, ['file', 'files'])) {
                $field_data[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED))));
            }

            // Scaffolding: Ensure template exists
            if (! $request->isAJAX() && ! $this->api_client && $final_type) {
                $this->builder->get_component($this->_set_theme, 'form', $final_type);
            }
        }

        // 3. Prepare Final Output
        $highest_column = 1;
        if (! empty($this->_field_position)) {
            $highest_column = max($this->_field_position);
        }

        // Merge query string with primary keys
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

        if (! empty($this->_field_order)) {
            $order_source = $this->_field_order;
        } elseif (! empty($this->_view_order)) {
            $order_source = $this->_view_order;
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
            // Merge flipped sorted keys with original data to reorder
            return array_replace(array_flip($sorted), $serialized);
        }

        return $serialized;
    }

    /**
     * Determine valid input type from type definition.
     */
    private function _determine_input_type(array $type): array
    {
        // Get intersection between defined types and valid builder types
        $field_type = array_intersect(array_keys($type), self::VALID_TYPES);

        if (empty($field_type)) {
            $field_type = ['text'];
        }

        if (count($field_type) > 1) {
            // If multiple valid types found, remove the last one (legacy logic behavior)
            array_pop($field_type);
        }

        return $field_type;
    }

    /**
     * Handle logic specific to 'Create' mode.
     * Includes setting default values and calculating 'Last Insert' custom IDs.
     */
    private function _handle_create_mode(string $field, array $type, mixed $value, bool $checked): array
    {
        if (isset($this->_default_value[$field])) {
            $value = $this->_default_value[$field];
        } elseif (array_key_exists('boolean', $type)) {
            $checked = true;
        }

        // Logic for custom Auto-Increment (e.g., INV/2024/0001)
        if (array_key_exists('last_insert', $type)) {
            if (! isset($this->_default_value[$field])) {
                $parameter = $type['last_insert']['parameter'];
                $extra_params = $type['last_insert']['alpha'];
                $type_key = array_search('{1}', explode('/', $parameter));

                // Construct CAST statement based on DB Driver
                $cast_field = in_array($this->_db_driver, ['SQLSRV'])
                    ? 'CONVERT(' . $field . ', SIGNED INTEGER)'
                    : 'CAST(' . $field . ' AS SIGNED INTEGER)';

                // Build Query
                if ($extra_params) {
                    $this->model->where($extra_params);
                }

                $max_func = (in_array($this->_db_driver, ['Postgre']) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $cast_field . '), 0) AS ' . $field;

                $last_insert = $this->model->select($max_func)
                    ->order_by($field, 'desc')
                    ->get($this->_table, 1)
                    ->row($field);

                // Process result
                if ($last_insert) {
                    $last_insert_parts = (strpos($last_insert, '/') !== false ? explode('/', $last_insert) : [$last_insert]);
                    $segment = $last_insert_parts[$type_key] ?? $last_insert_parts[0];
                    $last_insert = preg_replace('/[^0-9]/', '', $segment);
                }

                $next_val = (intval($last_insert) > 0 ? intval($last_insert) : 0) + 1;
                $value = $next_val;

                // Format with sprintf if requested
                if (array_key_exists('sprintf', $type)) {
                    $value = sprintf(($extra_params ?: '%04d'), $value);
                }

                // Replace placeholder in parameter
                if ($parameter && ! is_array($parameter)) {
                    $value = str_replace('{1}', $value, $parameter);
                }
            }
        }

        return [$value, $checked];
    }

    /**
     * Handle logic specific to 'Update' mode.
     * Includes marking selected options in Select/Checkbox/Radio.
     */
    private function _handle_update_mode(array $type, mixed $value, mixed $content): array
    {
        if (array_key_exists('select', $type) && is_array($content)) {
            foreach ($content as $key => $val) {
                $content[$key]['selected'] = ($value == $val['value']);
            }
        } elseif (array_intersect(['checkbox', 'radio'], array_keys($type)) && is_array($content)) {
            $has_checked = false;

            foreach ($content as $key => $val) {
                $content[$key]['checked'] = ($value == $val['value']);
                if (! $has_checked && $value == $val['value']) {
                    $has_checked = true;
                }
            }

            // Fallback: Check the first option if nothing matches (optional safety)
            if (! $has_checked && ! empty($content)) {
                $content[array_key_first($content)]['checked'] = true;
            }
        }

        // Apply sprintf formatting on update if defined
        if (array_key_exists('sprintf', $type)) {
            $parameter = $type['sprintf']['parameter'];
            $extra_params = $type['sprintf']['alpha'];
            $value = sprintf(($extra_params ?: '%04d'), $value);

            if ($parameter && ! is_array($parameter)) {
                $value = str_replace('{1}', $value, $parameter);
            }
        }

        return [$value, $content];
    }
}
