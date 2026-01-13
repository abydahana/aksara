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

namespace Aksara\Laboratory\Renderer;

use Aksara\Laboratory\Traits;

/**
 * Data Formatter Class
 *
 * This class is responsible for transforming raw data (from database or default values)
 * into a format suitable for display in Forms, Tables, or Views.
 * It handles logic for:
 * - Transforming JSON stored data into arrays (for checkboxes, files, etc.)
 * - Formatting Dates and Datetimes
 * - Constructing Hyperlinks with dynamic parameters
 * - Formatting Images and File paths
 * - Applying 'sprintf' formatting patterns
 */
class Formatter
{
    /**
     * Load traits to access dynamic properties (method, upload path, etc.)
     */
    use Traits;

    /**
     * Constructor
     *
     * @param   array $properties Associative array of properties to inject (context)
     */
    public function __construct(array $properties = [])
    {
        // Hydrate properties dynamically
        foreach ($properties as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * Format the field value based on its type definition.
     *
     * @param   string $field       The field name
     * @param   mixed  $value       The raw value
     * @param   array  $type        The type definition array (e.g. ['select' => [...]])
     * @param   array  $replacement Data for dynamic replacements (e.g. for hyperlinks)
     * @return  mixed  The formatted value
     */
    public function format(string $field, mixed $value = null, array $type = [], array $replacement = []): mixed
    {
        // Iterate through type definitions (usually only one primary type exists)
        foreach ($type as $key => $val) {
            // 1. Checkbox & Radio (Options)
            if (in_array($key, ['checkbox', 'radio'], true) && ! empty($val['parameter'])) {
                $value = $this->_formatCheckboxRadio($key, $field, $value, $val['parameter']);
            }
            // 2. Select (Options)
            elseif ('select' === $key && ! empty($val['parameter'])) {
                $value = $this->_formatSelect($field, $value, $val['parameter']);
            }
            // 3. Files & Images (Multiple)
            elseif (in_array($key, ['files', 'images'], true)) {
                $value = $this->_formatMultipleFiles($value);
            }
            // 4. Single File
            elseif ('file' === $key) {
                $value = get_file($this->_setUploadPath, $value);
            }
            // 5. Single Image
            elseif ('image' === $key) {
                $thumbMode = ! array_key_exists('original_thumbnail', $type) ? 'thumb' : null;
                $value = get_image($this->_setUploadPath, $value, $thumbMode);
            }
            // 6. Hyperlink
            elseif ('hyperlink' === $key) {
                $value = $this->_formatHyperlink($val, $replacement);
            }
            // 7. JSON Based Components (Attribution, Accordion)
            elseif (in_array($key, ['attribution', 'accordion'], true)) {
                if (is_string($value) && is_json($value)) {
                    $value = json_decode($value);
                }
            }
            // 8. Carousel
            elseif ('carousel' === $key) {
                $value = $this->_formatCarousel($value);
            }
            // 9. Geospatial
            elseif ('geospatial' === $key) {
                if (! $value || ! is_json($value)) {
                    $value = '{}';
                }
            }
            // 10. Date
            elseif ('date' === $key && $value && '0000-00-00' !== $value) {
                $timestamp = strtotime($value);
                if ($timestamp) {
                    $month = phrase(date('F', $timestamp));
                    $value = date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
                }
            }
            // 11. DateTime
            elseif ('datetime' === $key && $value && '0000-00-00 00:00:00' !== $value) {
                $timestamp = strtotime($value);
                if ($timestamp) {
                    $month = phrase(date('F', $timestamp));
                    $value = date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . ', ' . date('H:i:s', $timestamp);
                }
            }
            // 12. Sprintf Formatting
            elseif ('sprintf' === $key) {
                $value = $this->_formatSprintf($value, $val);
            }
        }

        return $value;
    }

    /**
     * Format Checkbox and Radio inputs.
     * Handles logic for 'create'/'update' modes (preparing options array) vs 'read' mode (translating value).
     */
    private function _formatCheckboxRadio(string $type, string $field, mixed $value, array $options): mixed
    {
        // Edit Mode (Create/Update): Return Array of Options with 'checked' state
        if (in_array($this->_method, ['create', 'update'], true)) {
            $checkedValues = $value;

            if ('checkbox' === $type && is_string($value) && is_json($value)) {
                $checkedValues = json_decode($value, true);
            }

            $formattedOptions = [];

            foreach ($options as $optKey => $optLabel) {
                $isChecked = false;

                // Determine checked state
                if (is_array($checkedValues) && in_array($optKey, $checkedValues, true)) {
                    $isChecked = true;
                } elseif (! is_array($checkedValues) && (string)$optKey === (string)$checkedValues) {
                    $isChecked = true;
                } elseif ('create' === $this->_method && isset($this->_defaultValue[$field]) && $this->_defaultValue[$field] == $optKey) {
                    $isChecked = true;
                }

                $formattedOptions[] = [
                    'value' => $optKey,
                    'label' => $optLabel,
                    'checked' => $isChecked
                ];
            }
            return $formattedOptions;
        }

        // Read Mode: Return Label
        return $options[$value] ?? null;
    }

    /**
     * Format Select inputs.
     * Handles logic for 'create'/'update' modes (preparing options array) vs 'read' mode (translating value).
     */
    private function _formatSelect(string $field, mixed $value, array $options): mixed
    {
        // Edit Mode
        if (in_array($this->_method, ['create', 'update'], true)) {
            $formattedOptions = [];
            foreach ($options as $optKey => $optLabel) {
                $isChecked = ((string) $optKey === (string) $value);
                if ('create' === $this->_method && isset($this->_defaultValue[$field]) && $this->_defaultValue[$field] == $optKey) {
                    $isChecked = true;
                }

                $formattedOptions[] = [
                    'value' => $optKey,
                    'label' => $optLabel,
                    'selected' => $isChecked
                ];
            }
            return $formattedOptions;
        }

        // Read Mode
        return $options[$value] ?? null;
    }

    /**
     * Format Multiple Files/Images (JSON to Array of Objects).
     */
    private function _formatMultipleFiles(mixed $value): mixed
    {
        if (is_string($value) && is_json($value)) {
            $filesData = json_decode($value);
            $filesList = [];

            if (is_object($filesData) || is_array($filesData)) {
                foreach ($filesData as $src => $alt) {
                    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);

                    $icon = $isImage ? get_image($this->_setUploadPath, $src, 'icon') : null;
                    $thumbnail = $isImage ? get_image($this->_setUploadPath, $src, 'thumb') : null;
                    $url = get_file($this->_setUploadPath, $src);

                    // Format file size
                    $filesize = get_filesize($this->_setUploadPath, $src);
                    $filesize = str_replace(['kb', 'mb', 'gb', 'b', '.'], '', strtolower((string)$filesize));

                    $filesList[] = [
                        'name' => $alt,
                        'file' => $src,
                        'size' => $filesize,
                        'url' => $url,
                        'icon' => $icon,
                        'thumbnail' => $thumbnail
                    ];
                }
            }
            return $filesList;
        }
        return $value;
    }

    /**
     * Format Hyperlinks with dynamic replacements.
     */
    private function _formatHyperlink(array $config, array $replacement): string
    {
        $parameter = $config['parameter'] ?? '';
        $alpha = $config['alpha'] ?? [];
        $queryParams = [
            'per_page' => null,
            'limit' => null
        ];

        // 1. Replace Magic Strings {{ key }} in Parameter (URL)
        if (is_string($parameter) && strpos($parameter, '{{') !== false) {
            preg_match_all('/\{\{(.*?)\}\}/', $parameter, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $idx => $key) {
                    $key = trim($key);
                    if (isset($replacement[$key])) {
                        $parameter = str_replace($matches[0][$idx], $replacement[$key], $parameter);
                    }
                }
            }
        }

        // 2. Process Query Params (Alpha)
        // Handle if alpha contains magic strings
        if (is_string($alpha) && strpos($alpha, '{{') !== false) {
            preg_match_all('/\{\{(.*?)\}\}/', $alpha, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $idx => $key) {
                    $key = trim($key);
                    if (isset($replacement[$key])) {
                        $alpha = str_replace($matches[0][$idx], $replacement[$key], $alpha);
                    }
                }
            }
            if (is_json($alpha)) {
                $alpha = json_decode($alpha, true);
            }
        }

        if (is_array($alpha)) {
            foreach ($alpha as $qKey => $qVal) {
                // Determine value from replacement array
                if (isset($replacement[$qVal])) {
                    $queryParams[$qKey] = $replacement[$qVal];
                } elseif (isset($replacement[$qKey])) {
                    // Backup check
                    $queryParams[$qKey] = $replacement[$qKey];
                } else {
                    // Raw value
                    $queryParams[$qKey] = $qVal;
                }
            }
        }

        // 3. Build Final URL
        // Check for external link
        if (preg_match('/^(http|https):\/\//', $parameter)) {
            return $parameter . '?' . http_build_query($queryParams);
        }

        return base_url($parameter, $queryParams);
    }

    /**
     * Format Carousel Data.
     */
    private function _formatCarousel(mixed $value): mixed
    {
        if (is_string($value) && is_json($value)) {
            $items = json_decode($value);
            $carousels = [];

            if (is_array($items) || is_object($items)) {
                foreach ($items as $item) {
                    // Ensure object property access
                    if (is_array($item)) {
                        $item = (object) $item;
                    }

                    $item->src = [
                        'background' => get_image($this->_setUploadPath, $item->background ?? ''),
                        'thumbnail' => get_image($this->_setUploadPath, $item->background ?? '', 'thumb'),
                        'placeholder' => get_image($this->_setUploadPath, 'placeholder.png', 'thumb')
                    ];

                    $carousels[] = $item;
                }
            }
            return $carousels;
        }
        return $value;
    }

    /**
     * Apply Sprintf Formatting.
     */
    private function _formatSprintf(mixed $value, array $config): string
    {
        $parameter = $config['parameter'] ?? '';
        $format = $config['alpha'] ?? '%04d';

        // Apply sprintf format
        $formattedValue = sprintf(($format ?: '%04d'), $value);

        // Replace placeholder {1} if exists
        if ($parameter && is_string($parameter)) {
            return str_replace('{1}', $formattedValue, $parameter);
        }

        return $formattedValue;
    }
}
