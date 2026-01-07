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

namespace Aksara\Modules\CMS\Controllers\Partials;

use CodeIgniter\Files\File;
use Aksara\Laboratory\Core;
use Throwable;

class Media extends Core
{
    private $_folders = [];
    private $_files = [];

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->setMethod('index');
    }

    public function index()
    {
        if ($this->request->getGet('action') == 'delete') {
            return $this->_delete_file($this->request->getGet('file'));
        }

        $directory = $this->request->getGet('directory');

        // Validasi dan normalisasi path
        $directory = $this->_sanitize_path($directory);

        $this->setTitle(phrase('Media'))
        ->setIcon('mdi mdi-folder-image')
        ->setOutput([
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
            if (empty($part) || '.' === $part) {
                continue;
            }

            // Handle parent directory references with validation
            if ('..' === $part) {
                // Only allow going up if we're not at the base directory
                if (! empty($result)) {
                    array_pop($result);
                }
                continue;
            }

            // Sanitize each part
            $part = preg_replace('/[^a-zA-Z0-9_\-\/\.]/', '', $part);

            // Add to result if not empty
            if (! empty($part)) {
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
            $fullPath = UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename;

            // Additional security check
            if (! $this->_is_within_upload_path($fullPath)) {
                return throw_exception(403, phrase('Access denied'));
            }

            unlink($fullPath);
        } catch (Throwable $e) {
            return throw_exception(403, $e->getMessage());
        }

        return throw_exception(301, phrase('The file was successfully removed.'), current_page(null, ['file' => null, 'action' => null]));
    }

    private function _is_within_upload_path($path)
    {
        $realUploadPath = realpath(UPLOAD_PATH);
        $realPath = realpath($path);

        if (false === $realPath) {
            return false;
        }

        // Check if the real path starts with the real upload path
        return strpos($realPath, $realUploadPath) === 0;
    }

    private function _directory_list($directory = null)
    {
        // Validate that directory is within allowed path
        if ($directory && ! $this->_is_valid_directory($directory)) {
            return throw_exception(403, phrase('Access denied'));
        }

        /* load required helper */
        helper('filesystem');

        $fullPath = UPLOAD_PATH;
        if ($directory) {
            $fullPath .= DIRECTORY_SEPARATOR . $directory;
        }

        // Additional security check
        if (! $this->_is_within_upload_path($fullPath)) {
            return throw_exception(403, phrase('Access denied'));
        }

        // Check if directory exists
        if (! is_dir($fullPath)) {
            $directory = null;
            $fullPath = UPLOAD_PATH;
        }

        // Map directory with depth 1
        $data = directory_map($fullPath, 1);

        if (is_array($data)) {
            // Define protected values (directory_map adds a trailing slash to folders)
            $protectedDirs = ['_extension/', '_import_tmp/', 'captcha/', 'logs/'];

            // Remove protected folders by comparing values
            $data = array_diff($data, $protectedDirs);

            // Optional: Re-index the array to 0, 1, 2...
            $data = array_values($data);
        }

        $filename = ($this->request->getGet('file') ? $this->_sanitize_path($this->request->getGet('file')) : null);
        $parentDirectory = ($directory ? $this->_get_parent_directory($directory) : null);

        if ($data) {
            $this->_parse_files($data, $directory);
        }

        $description = null;

        if ($filename && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
            // Security check for file access
            if (! $this->_is_within_upload_path(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
                return throw_exception(403, phrase('Access denied'));
            }

            $file = new File(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description = get_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description['icon'] = $this->_get_icon($directory, $filename);
            $description['mime_type'] = $file->getMimeType();
            $description['server_path'] = str_replace('\\', '/', $description['server_path']);
        }

        // Merge folder and files
        $data = array_merge($this->_folders, $this->_files);

        // Sort by folder first
        usort($data, function ($sourceA, $sourceB) {
            if ($sourceA['type'] == $sourceB['type']) {
                return strcmp($sourceA['label'], $sourceB['label']);
            }

            return ('directory' == $sourceA['type']) ? -1 : 1;
        });

        return [
            'parent_directory' => $parentDirectory,
            'directory' => $directory,
            'data' => $data,
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
        $dangerousPatterns = [
            '://', // URLs
            '\\\\', // UNC paths
            '%00', // Null bytes
        ];

        foreach ($dangerousPatterns as $pattern) {
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
                    $folderName = str_replace(DIRECTORY_SEPARATOR, '', $key);

                    // Skip protected directories
                    if (in_array($folderName, ['_extension', '_import_tmp', 'captcha', 'logs'], true)) {
                        continue;
                    }

                    $this->_folders[] = [
                        'source' => rtrim($folderName, DIRECTORY_SEPARATOR),
                        'label' => rtrim($folderName, DIRECTORY_SEPARATOR),
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

                        $file = new File(UPLOAD_PATH . ($directory ? DIRECTORY_SEPARATOR . $directory : null) . DIRECTORY_SEPARATOR . $val);
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

        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp'], true)) {
            $directory = (strpos($filename, DIRECTORY_SEPARATOR) !== false ? substr($filename, 0, strpos($filename, DIRECTORY_SEPARATOR)) : null);
        }

        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif'], true)) {
            return get_image($directory, $filename);
        } else {
            return get_image('_extension', $extension . '.png');
        }
    }
}
