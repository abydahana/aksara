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
    private mixed $apiClient = null;

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

        $primaryKey = [];
        $fieldData = [];
        $mergedFields = [];

        // Flatten merged fields for easy lookup
        if ($this->_mergeField) {
            foreach ($this->_mergeField as $key => $val) {
                $mergedFields = array_merge($mergedFields, $val);
            }
        }

        // 1. Sort Fields based on configuration
        $serialized = $this->_sortFields($serialized);

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
            $required = in_array('required', $validation, true);

            // Default label from field name
            $label = ucwords(str_replace('_', ' ', $field));
            $placeholder = $this->_setPlaceholder[$field] ?? null;
            $class = $this->_addClass[$field] ?? null;
            $readonly = null;

            // Collect Primary Key
            if ($primary) {
                $primaryKey[$field] = $value;
            }

            // Skip hidden fields or timestamps
            if ($hidden || array_intersect(['current_timestamp'], array_keys($type))) {
                continue;
            }

            // Override Labels
            if (isset($this->_mergeLabel[$field])) {
                $label = $this->_mergeLabel[$field];
            } elseif (isset($this->_setAlias[$field])) {
                $label = $this->_setAlias[$field];
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
            $fieldType = $this->_determineInputType($type);
            $finalType = end($fieldType); // Get the specific type (e.g., 'text')

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
                list($value, $checked) = $this->_handleCreateMode($field, $type, $value, $checked);
            } else {
                // UPDATE MODE
                list($value, $content) = $this->_handleUpdateMode($type, $value, $content);
            }

            // Prepare Field Data Structure
            $fieldData[$field] = [
                'name' => $field,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'type' => $finalType,
                'primary' => $primary,
                'maxlength' => $maxlength,
                'class' => $class,
                'placeholder' => $placeholder,
                'required' => $required,
                'checked' => $checked,
                'readonly' => $readonly,
                'accept' => gettype($value),
                'relation' => isset($this->_setRelation[$field]),
                'tooltip' => $this->_setTooltip[$field] ?? null,
                'attribution' => $this->_setAttribute[$field] ?? null,
                'position' => $this->_fieldPosition[$field] ?? 1,
                'prepend' => $this->_fieldPrepend[$field] ?? null,
                'append' => $this->_fieldAppend[$field] ?? null,
                'merged' => in_array($field, $mergedFields, true)
            ];

            // Specific handling for File/Image inputs
            if (in_array($finalType, ['image', 'images', 'carousel'], true)) {
                $fieldData[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED))));
                $fieldData[$field]['placeholder'] = get_image($this->_setUploadPath, 'placeholder.png', 'thumb');
            } elseif (in_array($finalType, ['file', 'files'], true)) {
                $fieldData[$field]['accept'] = implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED))));
            }

            // Scaffolding: Ensure template exists
            if (! $request->isAJAX() && ! $this->apiClient && $finalType) {
                $component = $this->builder->getComponent($this->_setTheme, 'form', $finalType);
            }
        }

        // 3. Prepare Final Output
        $highestColumn = 1;
        if (! empty($this->_fieldPosition)) {
            $highestColumn = max($this->_fieldPosition);
        }

        // Merge query string with primary keys
        $queryParams = array_replace($request->getGet(), $primaryKey);

        if ($this->apiClient) {
            unset($queryParams['aksara'], $queryParams['limit']);
        }

        return [
            'column_size' => $this->_columnSize,
            'column_total' => $highestColumn,
            'extra_action' => [
                'submit' => $this->_extraSubmit
            ],
            'form_size' => ($this->_modalSize ? str_replace('modal', 'form', $this->_modalSize) : ''),
            'field_size' => $this->_fieldSize,
            'field_data' => $fieldData,
            'merged_content' => $this->_mergeContent,
            'merged_field' => $this->_mergeField,
            'set_heading' => $this->_setHeading,
            'grouped_field' => $this->_groupField,
            'query_params' => $queryParams
        ];
    }

    /**
     * Sort fields based on controller configuration.
     */
    private function _sortFields(array $serialized): array
    {
        $orderSource = [];

        if (! empty($this->_fieldOrder)) {
            $orderSource = $this->_fieldOrder;
        } elseif (! empty($this->_viewOrder)) {
            $orderSource = $this->_viewOrder;
        } elseif (! empty($this->_columnOrder)) {
            $orderSource = $this->_columnOrder;
        }

        if (! empty($orderSource)) {
            $sorted = [];
            foreach ($orderSource as $val) {
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
    private function _determineInputType(array $type): array
    {
        // Get intersection between defined types and valid builder types
        $fieldType = array_intersect(array_keys($type), self::VALID_TYPES);

        if (empty($fieldType)) {
            $fieldType = ['text'];
        }

        if (count($fieldType) > 1) {
            // If multiple valid types found, remove the last one (legacy logic behavior)
            array_pop($fieldType);
        }

        return $fieldType;
    }

    /**
     * Handle logic specific to 'Create' mode.
     * Includes setting default values and calculating 'Last Insert' custom IDs.
     */
    private function _handleCreateMode(string $field, array $type, mixed $value, bool $checked): array
    {
        if (isset($this->_defaultValue[$field])) {
            $value = $this->_defaultValue[$field];
        } elseif (array_key_exists('boolean', $type)) {
            $checked = true;
        }

        // Logic for custom Auto-Increment (e.g., INV/2024/0001)
        if (array_key_exists('last_insert', $type)) {
            if (! isset($this->_defaultValue[$field])) {
                $parameter = $type['last_insert']['parameter'];
                $extraParams = $type['last_insert']['alpha'];
                $typeKey = array_search('{1}', explode('/', $parameter));

                // Construct CAST statement based on DB Driver
                $castField = in_array($this->_dbDriver, ['SQLSRV'], true)
                    ? 'CONVERT(' . $field . ', SIGNED INTEGER)'
                    : 'CAST(' . $field . ' AS SIGNED INTEGER)';

                // Build Query
                if ($extraParams) {
                    $this->model->where($extraParams);
                }

                $maxFunc = (in_array($this->_dbDriver, ['Postgre'], true) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $castField . '), 0) AS ' . $field;

                $lastInsert = $this->model->select($maxFunc)
                    ->orderBy($field, 'desc')
                    ->get($this->_table, 1)
                    ->row($field);

                // Process result
                if ($lastInsert) {
                    $lastInsertParts = (strpos($lastInsert, '/') !== false ? explode('/', $lastInsert) : [$lastInsert]);
                    $segment = $lastInsertParts[$typeKey] ?? $lastInsertParts[0];
                    $lastInsert = preg_replace('/[^0-9]/', '', $segment);
                }

                $nextVal = (intval($lastInsert) > 0 ? intval($lastInsert) : 0) + 1;
                $value = $nextVal;

                // Format with sprintf if requested
                if (array_key_exists('sprintf', $type)) {
                    $value = sprintf(($extraParams ?: '%04d'), $value);
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
    private function _handleUpdateMode(array $type, mixed $value, mixed $content): array
    {
        if (array_key_exists('select', $type) && is_array($content)) {
            foreach ($content as $key => $val) {
                $content[$key]['selected'] = ($value == $val['value']);
            }
        } elseif (array_intersect(['checkbox', 'radio'], array_keys($type)) && is_array($content)) {
            $hasChecked = false;

            foreach ($content as $key => $val) {
                $content[$key]['checked'] = ($value == $val['value']);
                if (! $hasChecked && $value == $val['value']) {
                    $hasChecked = true;
                }
            }

            // Fallback: Check the first option if nothing matches (optional safety)
            if (! $hasChecked && ! empty($content)) {
                $content[array_key_first($content)]['checked'] = true;
            }
        }

        // Apply sprintf formatting on update if defined
        if (array_key_exists('sprintf', $type)) {
            $parameter = $type['sprintf']['parameter'];
            $extraParams = $type['sprintf']['alpha'];
            $value = sprintf(($extraParams ?: '%04d'), $value);

            if ($parameter && ! is_array($parameter)) {
                $value = str_replace('{1}', $value, $parameter);
            }
        }

        return [$value, $content];
    }
}
