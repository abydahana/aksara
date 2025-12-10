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

namespace Aksara\Modules\Cms\Controllers\Partials;

class Media extends \Aksara\Laboratory\Core
{
    private $_folders = [];
    private $_files = [];

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->set_method('index');
    }

    public function index()
    {
        if (service('request')->getGet('action') == 'delete') {
            return $this->_delete_file(service('request')->getGet('file'));
        }

        $directory = service('request')->getGet('directory');

        // Validasi dan normalisasi path
        $directory = $this->_sanitize_path($directory);

        $this->set_title(phrase('Media'))
        ->set_icon('mdi mdi-folder-image')
        ->set_output([
            'results' => $this->_directory_list($directory)
        ])
        ->render();
    }

    private function _sanitize_path($path = null)
    {
        if (empty($path)) {
            return null;
        }

        // Decode URL encoding
        $path = urldecode($path);

        // Normalize directory separators
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Remove null bytes (protection against null byte attacks)
        $path = str_replace(chr(0), '', $path);

        // Remove trailing slash
        $path = rtrim($path, DIRECTORY_SEPARATOR);

        // Resolve relative paths
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $result = [];

        foreach ($parts as $part) {
            // Skip empty parts and current directory references
            if (empty($part) || $part === '.') {
                continue;
            }

            // Handle parent directory references with validation
            if ($part === '..') {
                // Only allow going up if we're not at the base directory
                if (!empty($result)) {
                    array_pop($result);
                }
                continue;
            }

            // Sanitize each part
            $part = preg_replace('/[^a-zA-Z0-9_\-\/\.]/', '', $part);

            // Add to result if not empty
            if (!empty($part)) {
                $result[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $result);
    }

    private function _delete_file($filename = '')
    {
        if (DEMO_MODE) {
            // Demo mode
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), current_page(null, ['file' => null, 'action' => null]));
        }

        try {
            // Sanitize filename before deletion
            $filename = $this->_sanitize_path($filename);

            // Ensure we're deleting within UPLOAD_PATH
            $full_path = UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename;

            // Additional security check
            if (!$this->_is_within_upload_path($full_path)) {
                return throw_exception(403, phrase('Access denied'));
            }

            unlink($full_path);
        } catch (\Throwable $e) {
            return throw_exception(403, $e->getMessage());
        }

        return throw_exception(301, phrase('The file was successfully removed.'), current_page(null, ['file' => null, 'action' => null]));
    }

    private function _is_within_upload_path($path)
    {
        $real_upload_path = realpath(UPLOAD_PATH);
        $real_path = realpath($path);

        if ($real_path === false) {
            return false;
        }

        // Check if the real path starts with the real upload path
        return strpos($real_path, $real_upload_path) === 0;
    }

    private function _directory_list($directory = null)
    {
        // Validate that directory is within allowed path
        if ($directory && !$this->_is_valid_directory($directory)) {
            return throw_exception(403, phrase('Access denied'));
        }

        /* load required helper */
        helper('filesystem');

        $full_path = UPLOAD_PATH;
        if ($directory) {
            $full_path .= DIRECTORY_SEPARATOR . $directory;
        }

        // Additional security check
        if (!$this->_is_within_upload_path($full_path)) {
            return throw_exception(403, phrase('Access denied'));
        }

        // Check if directory exists
        if (!is_dir($full_path)) {
            $directory = null;
            $full_path = UPLOAD_PATH;
        }

        $data = directory_map($full_path, 1); // Limit depth

        // Remove protected directories
        $protected_dirs = ['_extension', '_import_tmp', 'captcha', 'logs'];
        foreach ($protected_dirs as $protected_dir) {
            unset($data[$protected_dir . DIRECTORY_SEPARATOR]);
        }

        $filename = (service('request')->getGet('file') ? $this->_sanitize_path(service('request')->getGet('file')) : null);
        $parent_directory = ($directory ? $this->_get_parent_directory($directory) : null);
        $folders = [];
        $files = [];

        if ($data) {
            $this->_parse_files($data, $directory);
        }

        $description = null;

        if ($filename && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
            // Security check for file access
            if (!$this->_is_within_upload_path(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
                return throw_exception(403, phrase('Access denied'));
            }

            $file = new \CodeIgniter\Files\File(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description = get_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description['icon'] = $this->_get_icon($directory, $filename);
            $description['mime_type'] = $file->getMimeType();
            $description['server_path'] = str_replace('\\', '/', $description['server_path']);
        }

        return [
            'parent_directory' => $parent_directory,
            'directory' => $directory,
            'data' => array_merge($this->_folders, $this->_files),
            'description' => $description
        ];
    }

    private function _is_valid_directory($directory)
    {
        // Check for directory traversal attempts
        if (strpos($directory, '..') !== false) {
            return false;
        }

        // Check for absolute paths
        if (strpos($directory, DIRECTORY_SEPARATOR) === 0) {
            return false;
        }

        // Check for dangerous patterns
        $dangerous_patterns = [
            '://', // URLs
            '\\\\', // UNC paths
            '%00', // Null bytes
        ];

        foreach ($dangerous_patterns as $pattern) {
            if (strpos($directory, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }

    private function _get_parent_directory($directory)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $directory);

        if (count($parts) <= 1) {
            return null;
        }

        array_pop($parts);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    private function _parse_files($data = [], $directory = null)
    {
        if ($data) {
            foreach ($data as $key => $val) {
                if (strpos($key, DIRECTORY_SEPARATOR) !== false) {
                    $folder_name = str_replace(DIRECTORY_SEPARATOR, '', $key);

                    // Skip protected directories
                    if (in_array($folder_name, ['_extension', '_import_tmp', 'captcha', 'logs'])) {
                        continue;
                    }

                    $this->_folders[] = [
                        'source' => rtrim($folder_name, DIRECTORY_SEPARATOR),
                        'label' => rtrim($folder_name, DIRECTORY_SEPARATOR),
                        'type' => 'directory',
                        'icon' => base_url('assets/svg/folder')
                    ];
                } else {
                    if (is_array($val)) {
                        $this->_parse_files($val, $directory);
                    } else {
                        if (stripos($val, 'placeholder') !== false) {
                            continue;
                        }

                        $file = new \CodeIgniter\Files\File(UPLOAD_PATH . ($directory ? DIRECTORY_SEPARATOR . $directory : null) . DIRECTORY_SEPARATOR . $val);
                        $mime = $file->getMimeType();

                        if ('css' == strtolower(pathinfo($val, PATHINFO_EXTENSION))) {
                            $mime = 'text/css';
                        } elseif ('js' == strtolower(pathinfo($val, PATHINFO_EXTENSION))) {
                            $mime = 'text/javascript';
                        }

                        $this->_files[] = [
                            'source' => rtrim($val, DIRECTORY_SEPARATOR),
                            'label' => rtrim($val, DIRECTORY_SEPARATOR),
                            'type' => $mime,
                            'icon' => $this->_get_icon($directory, $val)
                        ];
                    }
                }
            }
        }
    }

    private function _get_icon($directory = null, $filename = null)
    {
        $filename = (strpos($filename, DIRECTORY_SEPARATOR) !== false ? substr($filename, strrpos($filename, DIRECTORY_SEPARATOR) + 1) : $filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp'])) {
            $directory = (strpos($filename, DIRECTORY_SEPARATOR) !== false ? substr($filename, 0, strpos($filename, DIRECTORY_SEPARATOR)) : null);
        }

        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
            return get_image($directory, $filename);
        } else {
            return get_image('_extension', $extension . '.png');
        }
    }
}
