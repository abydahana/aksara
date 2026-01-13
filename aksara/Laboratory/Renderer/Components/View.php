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
    private mixed $apiClient = null;

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

        $primaryKey = [];
        $fieldData = [];
        $mergedFields = [];

        // Flatten merged fields
        if ($this->_mergeField) {
            foreach ($this->_mergeField as $val) {
                $mergedFields = array_merge($mergedFields, $val);
            }
        }

        // 1. Sort Fields
        $serialized = $this->_sortFields($serialized);

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
                $primaryKey[$field] = $value;
            }

            // Skip hidden fields
            if ($hidden) {
                continue;
            }

            // Label Overrides
            if (isset($this->_mergeLabel[$field])) {
                $label = $this->_mergeLabel[$field];
            } elseif (isset($this->_setAlias[$field])) {
                $label = $this->_setAlias[$field];
            }

            // Determine Input Type
            $fieldType = $this->_getInputType($type);
            $finalType = end($fieldType);

            // Masking (Password/Encryption)
            if (array_intersect(['password', 'encryption'], array_keys($type))) {
                $value = '*****';
                $content = '*****';
            }

            // Handle Merged Content (Callbacks or Parsing)
            if (isset($this->_mergeContent[$field])) {
                $content = $this->_mergeContent($field, $replacement);
                $value = $content;
            }

            // Format Content
            $content = $this->formatter->format($field, $content, $type, $replacement);

            // Construct Field Data
            $fieldData[$field] = [
                'name' => $field,
                'label' => $label,
                'value' => $value,
                'content' => $content,
                'type' => $finalType,
                'primary' => $primary,
                'tooltip' => $this->_setTooltip[$field] ?? null,
                'position' => $this->_fieldPosition[$field] ?? 1,
                'prepend' => $this->_fieldPrepend[$field] ?? null,
                'append' => $this->_fieldAppend[$field] ?? null,
                'merged' => in_array($field, $mergedFields, true),
                'escape' => ! isset($this->_mergeContent[$field])
            ];

            // Type-specific adjustments
            if (in_array($finalType, ['image', 'images', 'carousel'], true)) {
                $fieldData[$field]['placeholder'] = get_image($this->_setUploadPath, 'placeholder.png', 'thumb');
            } elseif ('hyperlink' === $finalType && isset($type['hyperlink']['beta'])) {
                $fieldData[$field]['target'] = ($type['hyperlink']['beta'] ? '_blank' : null);
            }

            // Parse Twig within content (Double Parsing check)
            if (is_string($fieldData[$field]['content']) && strpos($fieldData[$field]['content'], '{{') !== false) {
                $fieldData[$field]['content'] = $this->parser->parse($fieldData[$field]['content'], $replacement);
            }

            // Scaffolding: Create template if missing
            if (! $request->isAJAX() && ! $this->apiClient && $finalType) {
                $component = $this->builder->getComponent($this->_setTheme, 'view');
            }

            // Add Class
            if ('hyperlink' === $finalType && isset($this->_addClass[$field])) {
                $fieldData[$field]['class'] = $this->_addClass[$field];
            }
        }

        // 4. Final Output Preparation
        $highestColumn = 1;
        if (! empty($this->_fieldPosition)) {
            $highestColumn = max($this->_fieldPosition);
        }

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

        if (! empty($this->_viewOrder)) {
            $orderSource = $this->_viewOrder;
        } elseif (! empty($this->_fieldOrder)) {
            $orderSource = $this->_fieldOrder;
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
            return array_replace(array_flip($sorted), $serialized);
        }

        return $serialized;
    }

    /**
     * Determine valid input type from type definition.
     */
    private function _getInputType(array $type): array
    {
        $fieldType = array_intersect(array_keys($type), self::VALID_TYPES);

        if (empty($fieldType)) {
            $fieldType = ['text'];
        }

        if (count($fieldType) > 1) {
            array_pop($fieldType);
        }

        return $fieldType;
    }

    /**
     * Process merge content using Callback or Parser.
     */
    private function _mergeContent(string $field, array $replacement): mixed
    {
        $mergeConfig = $this->_mergeContent[$field];

        if (! empty($mergeConfig['callback'])) {
            $router = Services::router();

            // Execute Controller Callback
            $namespace = $router->controllerName();
            $class = new $namespace();
            $callback = $mergeConfig['callback'];

            if (method_exists($class, $callback)) {
                return $class->$callback($replacement);
            }
        }

        // Execute String Parsing
        return $this->parser->parse($mergeConfig['parameter'], $replacement);
    }
}
