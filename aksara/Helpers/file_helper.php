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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

use Config\Services;
use CodeIgniter\Files\File;
use Aksara\Libraries\Storage;
use Aksara\Laboratory\Model;

if (! function_exists('get_file')) {
    /**
     * Get URL of uploaded file
     *
     * @param mixed|null $path
     * @param mixed|null $file
     */
    function get_file($path = null, $file = null)
    {
        $storage = get_active_storage();

        if ($storage) {
            return (new Storage($storage))->url(get_storage_object_path($path, $file));
        }

        return base_url(UPLOAD_PATH . '/' . ($path ? $path . '/' : null) . $file);
    }
}

if (! function_exists('get_image')) {
    /**
     * Get URL of uploaded image
     *
     * @param mixed|null $type
     * @param mixed|null $name
     * @param mixed|null $dimension
     */
    function get_image($type = null, $name = null, $dimension = null)
    {
        $storage = get_active_storage();

        if ($storage) {
            $name = $name ?: 'placeholder.png';

            return (new Storage($storage))->url(get_storage_object_path($type, $name, $dimension));
        }

        if ('thumb' == $dimension) {
            if (! file_exists(UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/placeholder.png')) {
                try {
                    if ($type && ! is_dir(UPLOAD_PATH . '/' . $type . '/thumbs')) {
                        // Try to make directory
                        mkdir(UPLOAD_PATH . '/' . $type . '/thumbs', 0755, true);
                    }

                    if (is_file(UPLOAD_PATH . '/placeholder_thumb.png')) {
                        // Copy placeholder image
                        copy(UPLOAD_PATH . '/placeholder_thumb.png', UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/placeholder.png');
                    }
                } catch (\Throwable $e) {
                    // Keep silent
                }
            }

            $placeholder = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/placeholder.png';
            $file = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/' . $name;
        } elseif ('icon' == $dimension) {
            if (! file_exists(UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/placeholder.png')) {
                try {
                    if ($type && ! is_dir(UPLOAD_PATH . '/' . $type . '/icons')) {
                        // Try to make directory
                        mkdir(UPLOAD_PATH . '/' . $type . '/icons', 0755, true);
                    }

                    if (is_file(UPLOAD_PATH . '/placeholder_icon.png')) {
                        // Copy placeholder image
                        copy(UPLOAD_PATH . '/placeholder_icon.png', UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/placeholder.png');
                    }
                } catch (\Throwable $e) {
                    // Keep silent
                }
            }

            $placeholder = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/placeholder.png';
            $file = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/' . $name;
        } else {
            if (! file_exists(UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'placeholder.png')) {
                try {
                    if ($type && ! is_dir(UPLOAD_PATH . '/' . $type)) {
                        // Try to make directory
                        mkdir(UPLOAD_PATH . '/' . $type, 0755, true);
                    }

                    if (is_file(UPLOAD_PATH . '/placeholder.png')) {
                        // Copy placeholder image
                        copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'placeholder.png');
                    }
                } catch (\Throwable $e) {
                    // Keep silent
                }
            }

            $placeholder = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'placeholder.png';
            $file = UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . $name;
        }

        if (is_file($file) && file_exists($file)) {
            $image = $file;
        } else {
            $image = $placeholder;
        }

        $method = substr(uri_string(), strrpos(uri_string(), '/') + 1);

        if ((in_array(service('request')->getGet('method'), ['print', 'embed', 'pdf', 'download']) || 'document' == service('request')->getGet('r')) && 'print' != $method && 'embed' != $method) {
            $type = pathinfo(ROOTPATH . $image, PATHINFO_EXTENSION);
            $data = file_get_contents($image);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return base_url($image);
    }
}

if (! function_exists('resize_image')) {
    /**
     * Image resize
     *
     * @param null|mixed $source
     */
    function resize_image($source = null)
    {
        if (file_exists($source)) {
            $path = dirname($source);
            $filename = basename($source);

            try {
                if (! is_dir($path . '/thumbs')) {
                    // Directory is not exists, create one
                    mkdir($path . '/thumbs', 0755, true);
                    if (is_file(UPLOAD_PATH . '/placeholder_thumb.png')) {
                        copy(UPLOAD_PATH . '/placeholder_thumb.png', $path . '/thumbs/placeholder.png');
                    }
                }

                if (! is_dir($path . '/icons')) {
                    // Directory is not exists, create one
                    mkdir($path . '/icons', 0755, true);
                    if (is_file(UPLOAD_PATH . '/placeholder_icon.png')) {
                        copy(UPLOAD_PATH . '/placeholder_icon.png', $path . '/icons/placeholder.png');
                    }
                }

                // Uploaded file is image format, prepare image manipulation
                $imageinfo = getimagesize($source);
                $source = new File($source);
                $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
                $original_dimension = (is_numeric(IMAGE_DIMENSION) ? IMAGE_DIMENSION : 1024);
                $thumbnail_dimension = (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 256);
                $icon_dimension = (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 64);

                // Load image manipulation library
                $image = Services::image('gd');

                if ($source->getMimeType() != 'image/gif' && $imageinfo[0] > $original_dimension) {
                    // Resize image and move to upload directory
                    $image->withFile($source)
                        ->resize($original_dimension, $original_dimension, true, $master_dimension)
                        ->save($path . '/' . $filename);
                }

                // Create thumbnail
                if ($image->withFile($source)->resize($thumbnail_dimension, $thumbnail_dimension, true, $master_dimension)->save($path . '/thumbs/' . $filename)) {
                    // Crop image after resized
                    $image->withFile($path . '/thumbs/' . $filename)
                        ->fit($thumbnail_dimension, $thumbnail_dimension, 'center')
                        ->save($path . '/thumbs/' . $filename);
                }

                // Create icon
                if ($image->withFile($source)->resize($icon_dimension, $icon_dimension, true, $master_dimension)->save($path . '/icons/' . $filename)) {
                    // Crop image after resized
                    $image->withFile($path . '/icons/' . $filename)
                        ->fit($icon_dimension, $icon_dimension, 'center')
                        ->save($path . '/icons/' . $filename);
                }
            } catch (\Throwable $e) {
                // Safe abstraction
            }
        }
    }
}

if (! function_exists('get_filesize')) {
    /**
     * Get file size
     *
     * @param mixed|null $path
     * @param mixed|null $file
     */
    function get_filesize($path = null, $file = null)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $filePath = FCPATH . UPLOAD_PATH . '/' . ($path ? $path . '/' : null) . $file;
        $bytes = (is_file($filePath) ? @filesize($filePath) : 0);

        if (! $bytes || $bytes <= 0) {
            return '0 B';
        }

        $factor = (int) floor((strlen((string) $bytes) - 1) / 3);

        return sprintf('%.2f', ($bytes / pow(1024, $factor))) . ' ' . ($size[$factor] ?? '');
    }
}

if (! function_exists('get_storage_object_path')) {
    /**
     * Build cloud object path relative to UPLOAD_PATH.
     * @param null|mixed $path
     * @param null|mixed $file
     * @param null|mixed $dimension
     */
    function get_storage_object_path($path = null, $file = null, $dimension = null): string
    {
        $segments = [];

        if ($path) {
            $segments[] = trim(str_replace('\\', '/', $path), '/');
        }

        if ('thumb' == $dimension) {
            $segments[] = 'thumbs';
        } elseif ('icon' == $dimension) {
            $segments[] = 'icons';
        }

        if ($file) {
            $segments[] = trim(str_replace('\\', '/', $file), '/');
        }

        return trim(implode('/', array_filter($segments, 'strlen')), '/');
    }
}

if (! function_exists('get_active_storage')) {
    /**
     * Get active cloud storage configuration with request and shared cache.
     */
    function get_active_storage(bool $refresh = false): ?object
    {
        static $storage = false;
        $cache = Services::cache();

        if ($refresh) {
            $storage = false;
            $cache->delete('aksara_active_storage');
        }

        if (false !== $storage) {
            return $storage;
        }

        $cached = $cache->get('aksara_active_storage');

        if (is_array($cached)) {
            return $storage = (object) $cached;
        } elseif (is_object($cached)) {
            return $storage = $cached;
        }

        try {
            $model = new Model();
            $config = $model->getWhere('app_storage', ['status' => 1], 1)->row();

            if (! $config || 'disabled' === strtolower((string) ($config->provider ?? ''))) {
                log_message('debug', 'No active cloud storage configuration found.');

                return $storage = null;
            }

            $cache->save('aksara_active_storage', (array) $config, 300);

            return $storage = $config;
        } catch (\Throwable $e) {
            log_message('error', 'Unable to load active cloud storage: ' . $e->getMessage());

            return $storage = null;
        }
    }
}
