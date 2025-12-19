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
            if (in_array($key, ['checkbox', 'radio']) && ! empty($val['parameter'])) {
                $value = $this->_format_checkbox_radio($key, $field, $value, $val['parameter']);
            }
            // 2. Select (Options)
            elseif ('select' === $key && ! empty($val['parameter'])) {
                $value = $this->_format_select($value, $val['parameter']);
            }
            // 3. Files & Images (Multiple)
            elseif (in_array($key, ['files', 'images'])) {
                $value = $this->_format_multiple_files($value);
            }
            // 4. Single File
            elseif ('file' === $key) {
                $value = get_file($this->_set_upload_path, $value);
            }
            // 5. Single Image
            elseif ('image' === $key) {
                $thumb_mode = ! array_key_exists('original_thumbnail', $type) ? 'thumb' : null;
                $value = get_image($this->_set_upload_path, $value, $thumb_mode);
            }
            // 6. Hyperlink
            elseif ('hyperlink' === $key) {
                $value = $this->_format_hyperlink($val, $replacement);
            }
            // 7. JSON Based Components (Attribution, Accordion)
            elseif (in_array($key, ['attribution', 'accordion'])) {
                if (is_string($value) && is_json($value)) {
                    $value = json_decode($value);
                }
            }
            // 8. Carousel
            elseif ('carousel' === $key) {
                $value = $this->_format_carousel($value);
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
                $value = $this->_format_sprintf($value, $val);
            }
        }

        return $value;
    }

    /**
     * Format Checkbox and Radio inputs.
     * Handles logic for 'create'/'update' modes (preparing options array) vs 'read' mode (translating value).
     */
    private function _format_checkbox_radio(string $type, string $field, mixed $value, array $options): mixed
    {
        // Edit Mode (Create/Update): Return Array of Options with 'checked' state
        if (in_array($this->_method, ['create', 'update'])) {
            $checked_values = $value;

            if ('checkbox' === $type && is_string($value) && is_json($value)) {
                $checked_values = json_decode($value, true);
            }

            $formatted_options = [];

            foreach ($options as $opt_key => $opt_label) {
                $is_checked = false;

                // Determine checked state
                if (is_array($checked_values) && in_array($opt_key, $checked_values)) {
                    $is_checked = true;
                } elseif (! is_array($checked_values) && (string)$opt_key === (string)$checked_values) {
                    $is_checked = true;
                } elseif ('create' === $this->_method && isset($this->_default_value[$field]) && $this->_default_value[$field] == $opt_key) {
                    $is_checked = true;
                }

                $formatted_options[] = [
                    'value' => $opt_key,
                    'label' => $opt_label,
                    'checked' => $is_checked
                ];
            }
            return $formatted_options;
        }

        // Read Mode: Return Label
        return $options[$value] ?? null;
    }

    /**
     * Format Select inputs.
     * Handles logic for 'create'/'update' modes (preparing options array) vs 'read' mode (translating value).
     */
    private function _format_select(mixed $value, array $options): mixed
    {
        // Edit Mode
        if (in_array($this->_method, ['create', 'update'])) {
            $formatted_options = [];
            foreach ($options as $opt_key => $opt_label) {
                $formatted_options[] = [
                    'value' => $opt_key,
                    'label' => $opt_label,
                    'selected' => ((string)$opt_key === (string)$value)
                ];
            }
            return $formatted_options;
        }

        // Read Mode
        return $options[$value] ?? null;
    }

    /**
     * Format Multiple Files/Images (JSON to Array of Objects).
     */
    private function _format_multiple_files(mixed $value): mixed
    {
        if (is_string($value) && is_json($value)) {
            $files_data = json_decode($value);
            $files_list = [];

            if (is_object($files_data) || is_array($files_data)) {
                foreach ($files_data as $src => $alt) {
                    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
                    $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

                    $icon = $is_image ? get_image($this->_set_upload_path, $src, 'icon') : null;
                    $thumbnail = $is_image ? get_image($this->_set_upload_path, $src, 'thumb') : null;
                    $url = get_file($this->_set_upload_path, $src);

                    // Format file size
                    $filesize = get_filesize($this->_set_upload_path, $src);
                    $filesize = str_replace(['kb', 'mb', 'gb', 'b', '.'], '', strtolower((string)$filesize));

                    $files_list[] = [
                        'name' => $alt,
                        'file' => $src,
                        'size' => $filesize,
                        'url' => $url,
                        'icon' => $icon,
                        'thumbnail' => $thumbnail
                    ];
                }
            }
            return $files_list;
        }
        return $value;
    }

    /**
     * Format Hyperlinks with dynamic replacements.
     */
    private function _format_hyperlink(array $config, array $replacement): string
    {
        $parameter = $config['parameter'] ?? '';
        $alpha = $config['alpha'] ?? [];
        $query_params = [
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
            foreach ($alpha as $q_key => $q_val) {
                // Determine value from replacement array
                if (isset($replacement[$q_val])) {
                    $query_params[$q_key] = $replacement[$q_val];
                } elseif (isset($replacement[$q_key])) {
                    // Backup check
                    $query_params[$q_key] = $replacement[$q_key];
                } else {
                    // Raw value
                    $query_params[$q_key] = $q_val;
                }
            }
        }

        // 3. Build Final URL
        // Check for external link
        if (preg_match('/^(http|https):\/\//', $parameter)) {
            return $parameter . '?' . http_build_query($query_params);
        }

        return base_url($parameter, $query_params);
    }

    /**
     * Format Carousel Data.
     */
    private function _format_carousel(mixed $value): mixed
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
                        'background' => get_image($this->_set_upload_path, $item->background ?? ''),
                        'thumbnail' => get_image($this->_set_upload_path, $item->background ?? '', 'thumb'),
                        'placeholder' => get_image($this->_set_upload_path, 'placeholder.png', 'thumb')
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
    private function _format_sprintf(mixed $value, array $config): string
    {
        $parameter = $config['parameter'] ?? '';
        $format = $config['alpha'] ?? '%04d';

        // Apply sprintf format
        $formatted_value = sprintf(($format ?: '%04d'), $value);

        // Replace placeholder {1} if exists
        if ($parameter && is_string($parameter)) {
            return str_replace('{1}', $formatted_value, $parameter);
        }

        return $formatted_value;
    }
}
