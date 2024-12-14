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

class Formatter
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
    }

    public function format($field, $value = null, $type = [], $replacement = [])
    {
        foreach ($type as $key => $val) {
            if (in_array($key, ['checkbox', 'radio']) && $val['parameter']) {
                if (in_array($this->_method, ['create', 'update'])) {
                    if ('checkbox' === $key && is_json($value)) {
                        // Indicates the value is JSON format
                        $checked = json_decode($value, true);
                    } else {
                        $checked = $value;
                    }

                    $value = [];

                    foreach ($val['parameter'] as $key => $val) {
                        $is_checked = false;

                        if (
                            is_array($checked) && in_array($key, $checked) ||
                            ! is_array($checked) && $key === $checked ||
                            ('create' === $this->_method && isset($this->_default_value[$field]) && $this->_default_value[$field] == $key)
                        ) {
                            // Set checked
                            $is_checked = true;
                        }

                        // Iterate array key pairs
                        $value[] = [
                            'value' => $key,
                            'label' => $val,
                            'checked' => $is_checked
                        ];
                    }
                } else {
                    if (isset($val['parameter'][$value])) {
                        $value = $val['parameter'][$value];
                    } else {
                        $value = null;
                    }
                }
            } elseif (in_array($key, ['select']) && $val['parameter']) {
                if (in_array($this->_method, ['create', 'update'])) {
                    $selected = $value;
                    $value = [];

                    foreach ($val['parameter'] as $key => $val) {
                        // Iterate array key pairs
                        $value[] = [
                            'value' => $key,
                            'label' => $val,
                            'selected' => $key == $selected
                        ];
                    }
                } else {
                    if (isset($val['parameter'][$value])) {
                        $value = $val['parameter'][$value];
                    } else {
                        $value = null;
                    }
                }
            } elseif (in_array($key, ['files', 'images'])) {
                if (is_json($value)) {
                    // Decode json value
                    $value = json_decode($value);
                    $files = [];

                    foreach ($value as $src => $alt) {
                        // Add new property to json
                        $icon = (in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) ? get_image($this->_set_upload_path, $src, 'icon') : null);
                        $thumbnail = (in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) ? get_image($this->_set_upload_path, $src, 'thumb') : null);
                        $url = get_file($this->_set_upload_path, $src);
                        $filesize = get_filesize($this->_set_upload_path, $src);
                        $filesize = str_replace(['kb', 'mb', 'gb', 'b', '.'], '', strtolower($filesize));
                        $files[] = [
                            'name' => $alt,
                            'file' => $src,
                            'size' => $filesize,
                            'url' => $url,
                            'icon' => $icon,
                            'thumbnail' => $thumbnail
                        ];
                    }

                    $value = $files;
                }
            } elseif (in_array($key, ['file'])) {
                // Set file url
                $value = get_file($this->_set_upload_path, $value);
            } elseif (in_array($key, ['image'])) {
                // Set image url
                $value = get_image($this->_set_upload_path, $value, 'thumb');
            } elseif (in_array($key, ['hyperlink'])) {
                // Prepare query string
                $query_string = [];

                if (is_string($val['parameter']) && strpos($val['parameter'], '{{') !== false && strpos($val['parameter'], '}}') !== false) {
                    // Extract magic string
                    preg_match_all('/\{\{(.*?)\}\}/', $val['parameter'], $matches);

                    if (isset($matches[1]) && $matches[1]) {
                        foreach ($matches[1] as $_key => $_val) {
                            // Trim whitespace for matches string
                            $_val = trim($_val);

                            if (isset($replacement[$_val])) {
                                // Replace magic string
                                $val['parameter'] = str_replace($matches[0][$_key], $replacement[$_val], $val['parameter']);
                            }
                        }
                    }
                }

                if (is_string($val['alpha']) && strpos($val['alpha'], '{{') !== false && strpos($val['alpha'], '}}') !== false) {
                    // Extract magic string
                    preg_match_all('/\{\{(.*?)\}\}/', $val['alpha'], $matches);

                    if (isset($matches[1]) && $matches[1]) {
                        foreach ($matches[1] as $_key => $_val) {
                            // Trim whitespace for matches string
                            $_val = trim($_val);

                            if (isset($replacement[$_val])) {
                                // Replace magic string
                                $val['alpha'] = str_replace($matches[0][$_key], $replacement[$_val], $val['alpha']);
                            }
                        }
                    }

                    if (is_json($val['alpha'])) {
                        // Decode JSON data
                        $val['alpha'] = json_decode($val['alpha'], true);
                    }
                }

                if (is_array($val['alpha'])) {
                    // Attempt to get query string value from field
                    foreach ($val['alpha'] as $_key => $_val) {
                        if (isset($replacement[$_val]) || in_array($_val, array_keys($replacement))) {
                            // Found query string value
                            $_val = (isset($replacement[$_val]) ? $replacement[$_val] : null);
                        } elseif (isset($replacement[$_key])) {
                            // Found query string backup value
                            $_val = $replacement[$_key];
                        }

                        $query_string[$_key] = $_val;
                    }
                }

                // Check if URL is contain external link
                preg_match('/(http|https):\/\/[a-z0-9]+[a-z0-9_\/]*/', $val['parameter'], $external);

                if (! $external) {
                    // It's local link
                    $value = base_url($val['parameter'], $query_string);
                } else {
                    // External link
                    $value = $val['parameter'] . '?' . http_build_query($query_string);
                }
            } elseif (in_array($key, ['attribution', 'accordion'])) {
                if (is_json($value)) {
                    // Decode json value
                    $value = json_decode($value);
                }
            } elseif (in_array($key, ['carousel'])) {
                if (is_json($value)) {
                    // Decode json value
                    $value = json_decode($value);
                    $carousels = [];

                    foreach ($value as $index => $carousel) {
                        // Format image source
                        $carousel->src = [];
                        $carousel->src['background'] = get_image($this->_set_upload_path, $carousel->background);
                        $carousel->src['thumbnail'] = get_image($this->_set_upload_path, $carousel->background, 'thumb');
                        $carousel->src['placeholder'] = get_image($this->_set_upload_path, 'placeholder.png', 'thumb');

                        $carousels[] = $carousel;
                    }

                    $value = $carousels;
                }
            } elseif (in_array($key, ['geospatial'])) {
                if (! $value || ! is_json($value)) {
                    $value = '{}';
                }
            } elseif (in_array($key, ['custom_format'])) {
                $value = $val['parameter'];
            } elseif (in_array($key, ['date']) && $value && '0000-00-00' !== $value) {
                $timestamp = strtotime($value ?? '0000-00-00');
                $month = date('F', $timestamp);
                $month = phrase($month);
                $value = date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
            } elseif (in_array($key, ['datetime']) && $value && '0000-00-00 00:00:00' !== $value) {
                $timestamp = strtotime($value ?? '0000-00-00 00:00:00');
                $month = date('F', $timestamp);
                $month = phrase($month);
                $value = date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . ', ' . date('H:i:s', $timestamp);
            } elseif (in_array($key, ['sprintf'])) {
                $parameter = $val['parameter'];
                $extra_params = $val['alpha'];
                $value = sprintf(($extra_params ? $extra_params : '%04d'), $value);

                if ($parameter && ! is_array($parameter)) {
                    $value = str_replace('{1}', $value, $parameter);
                }
            }
        }

        return $value;
    }
}
