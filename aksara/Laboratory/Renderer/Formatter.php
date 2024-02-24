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

    public function format($value = null, $type = [], $replacement = [])
    {
        foreach ($type as $key => $val) {
            if (in_array($key, ['checkbox', 'radio']) && $val['parameter']) {
                if (in_array($this->_method, ['create', 'update'])) {
                    $checked = $value;
                    $value = [];

                    foreach ($val['parameter'] as $key => $val) {
                        // Iterate array key pairs
                        $value[] = [
                            'value' => $key,
                            'label' => $val,
                            'checked' => $key == $checked
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

                if (is_string($val['alpha']) && strpos($val['alpha'], '{{') !== false && strpos($val['alpha'], '}}')) {
                    preg_match('/\{\{(.*?)\}\}/', $val['alpha'], $matches);

                    // Trim whitespace for matches string
                    $matches = array_map('trim', $matches);

                    if (isset($replacement[$matches[1]])) {
                        if (is_json($replacement[$matches[1]])) {
                            // Decode JSON data
                            $val['alpha'] = json_decode($replacement[$matches[1]], true);
                        } else {
                            $val['alpha'] = $replacement[$matches[1]];
                        }
                    }
                }

                if (is_array($val['alpha'])) {
                    // Attempt to get query string value from field
                    foreach ($val['alpha'] as $_key => $_val) {
                        if (isset($replacement[$_val])) {
                            // Found query string value
                            $_val = $replacement[$_val];
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
            }
        }

        return $value;
    }
}
