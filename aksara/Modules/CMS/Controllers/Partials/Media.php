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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Throwable;
use CodeIgniter\Files\File;
use Aksara\Laboratory\Core;
use Aksara\Libraries\Storage;

class Media extends Core
{
    private array $_folders = [];
    private array $_files = [];
    private ?object $_storage = null;

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();
        $this->setMethod('index');
        $this->setPermission();
        $this->setTheme('backend');

        if (! function_exists('get_active_storage')) {
            helper('file');
        }

        $this->_storage = get_active_storage();
    }

    public function index()
    {
        if ($this->request->getPost('hide_cloud_storage_media_notice')) {
            set_userdata('hide_cloud_storage_media_notice', true);

            return make_json([
                'status' => 200
            ]);
        }

        if ($this->request->getGet('action') == 'delete') {
            return $this->_deleteFile($this->request->getGet('file'));
        }

        $directory = $this->request->getGet('directory');

        // Validasi dan normalisasi path
        $directory = $this->_sanitizePath($directory);

        $this->setTitle(phrase('Media'))
        ->setIcon('mdi mdi-folder-image')
        ->setOutput([
            'results' => $this->_directoryList($directory)
        ])
        ->render();
    }

    private function _sanitizePath($path = null)
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

    private function _deleteFile($filename = '')
    {
        if (DEMO_MODE) {
            // Demo mode
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), current_page(null, ['file' => null, 'action' => null]));
        }

        try {
            // Sanitize filename before deletion
            $filename = $this->_sanitizePath($filename);

            if ($this->_storage) {
                (new Storage($this->_storage))->delete($this->_remotePath($filename));

                return throw_exception(301, phrase('The file was successfully removed.'), current_page(null, ['file' => null, 'action' => null]));
            }

            // Ensure we're deleting within UPLOAD_PATH
            $fullPath = UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename;

            // Additional security check
            if (! $this->_isWithinUploadPath($fullPath)) {
                return throw_exception(403, phrase('Access denied'));
            }

            unlink($fullPath);
        } catch (Throwable $e) {
            return throw_exception(403, $e->getMessage());
        }

        return throw_exception(301, phrase('The file was successfully removed.'), current_page(null, ['file' => null, 'action' => null]));
    }

    private function _isWithinUploadPath(string $path): bool
    {
        $realUploadPath = realpath(UPLOAD_PATH);
        $realPath = realpath($path);

        if (false === $realPath) {
            return false;
        }

        // Check if the real path starts with the real upload path
        return strpos($realPath, $realUploadPath) === 0;
    }

    private function _directoryList($directory = null)
    {
        if ($this->_storage) {
            return $this->_cloudDirectoryList($directory);
        }

        // Validate that directory is within allowed path
        if ($directory && ! $this->_isValidDirectory($directory)) {
            return throw_exception(403, phrase('Access denied'));
        }

        // Load required helper
        helper('filesystem');

        $fullPath = UPLOAD_PATH;
        if ($directory) {
            $fullPath .= DIRECTORY_SEPARATOR . $directory;
        }

        // Additional security check
        if (! $this->_isWithinUploadPath($fullPath)) {
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
            $protectedDirs = ['_extension/', '_import_tmp/', 'captcha/', 'logs/', '.Spotlight-V100/', '.Trashes/', '__MACOSX/'];

            // Remove protected folders by comparing values
            $data = array_diff($data, $protectedDirs);

            // Optional: Re-index the array to 0, 1, 2...
            $data = array_values($data);
        }

        $filename = ($this->request->getGet('file') ? $this->_sanitizePath($this->request->getGet('file')) : null);
        $parentDirectory = ($directory ? $this->_getParentDirectory($directory) : null);

        if ($data) {
            $this->_parseFiles($data, $directory);
        }

        $description = null;

        if ($filename && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
            // Security check for file access
            if (! $this->_isWithinUploadPath(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename)) {
                return throw_exception(403, phrase('Access denied'));
            }

            $file = new File(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description = get_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            $description['icon'] = $this->_getIcon($directory, $filename);
            $description['mime_type'] = $file->getMimeType();
            $description['server_path'] = str_replace('\\', '/', $description['server_path'] ?? '');
            $description['formatted_size'] = $this->_formatBytes((int) ($description['size'] ?? 0));
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
            'cloud_storage' => false,
            'data' => $data,
            'description' => $description
        ];
    }

    private function _cloudDirectoryList($directory = null): array
    {
        if ($directory && ! $this->_isValidDirectory($directory)) {
            return throw_exception(403, phrase('Access denied'));
        }

        $storage = new Storage($this->_storage);
        $directory = $directory ? trim(str_replace('\\', '/', $directory), '/') : null;
        $parentDirectory = ($directory ? $this->_getParentDirectory(str_replace('/', DIRECTORY_SEPARATOR, $directory)) : null);
        $filename = ($this->request->getGet('file') ? $this->_remotePath($this->_sanitizePath($this->request->getGet('file'))) : null);

        try {
            foreach ($storage->listContents($directory ?: '', false) as $item) {
                $path = trim($item->path(), '/');
                $name = basename($path);

                if (! $name || $this->_isIgnoredMediaPath($path) || stripos($name, 'placeholder') !== false) {
                    continue;
                }

                if (method_exists($item, 'isDir') && $item->isDir()) {
                    $this->_folders[] = [
                        'source' => $name,
                        'label' => $name,
                        'type' => 'directory',
                        'icon' => base_url('assets/svg/folder')
                    ];
                } elseif (method_exists($item, 'isFile') && $item->isFile()) {
                    $this->_files[] = [
                        'source' => $name,
                        'label' => $name,
                        'type' => $this->_mimeType($path),
                        'icon' => $this->_getIcon(dirname($path) == '.' ? null : dirname($path), $name)
                    ];
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'Unable to list cloud media: ' . $e->getMessage());
        }

        $description = null;

        if ($filename && $storage->exists($filename)) {
            $description = [
                'name' => basename($filename),
                'server_path' => $storage->url($filename),
                'url' => $storage->url($filename),
                'icon' => $this->_getIcon(dirname($filename) == '.' ? null : dirname($filename), basename($filename)),
                'mime_type' => $this->_mimeType($filename),
                'size' => $this->_fileSize($filename),
                'formatted_size' => $this->_formatBytes($this->_fileSize($filename)),
                'date' => $this->_lastModified($filename)
            ];
        }

        $data = array_merge($this->_folders, $this->_files);

        usort($data, function ($sourceA, $sourceB) {
            if ($sourceA['type'] == $sourceB['type']) {
                return strcmp($sourceA['label'], $sourceB['label']);
            }

            return ('directory' == $sourceA['type']) ? -1 : 1;
        });

        return [
            'parent_directory' => $parentDirectory ? str_replace(DIRECTORY_SEPARATOR, '/', $parentDirectory) : null,
            'directory' => $directory,
            'cloud_storage' => true,
            'data' => $data,
            'description' => $description
        ];
    }

    private function _isValidDirectory(?string $directory = null): bool
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

    private function _getParentDirectory(?string $directory = null)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $directory);

        if (count($parts) <= 1) {
            return null;
        }

        array_pop($parts);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    private function _parseFiles($data = [], $directory = null)
    {
        if ($data) {
            foreach ($data as $key => $val) {
                if (strpos($key, DIRECTORY_SEPARATOR) !== false) {
                    $folderName = str_replace(DIRECTORY_SEPARATOR, '', $key);

                    // Skip protected directories
                    if (in_array($folderName, ['_extension', '_import_tmp', 'captcha', 'logs'])) {
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
                        $this->_parseFiles($val, $directory);
                    } else {
                        if (stripos($val, 'placeholder') !== false || $this->_isIgnoredMediaPath(($directory ? $directory . DIRECTORY_SEPARATOR : null) . $val)) {
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
                            'icon' => $this->_getIcon($directory, $val)
                        ];
                    }
                }
            }
        }
    }

    private function _remotePath(?string $path = null): string
    {
        return trim(str_replace('\\', '/', (string) $path), '/');
    }

    private function _isProtectedPath(string $path): bool
    {
        $first = strtok(trim($path, '/'), '/');

        return in_array($first, ['_extension', '_import_tmp', 'captcha', 'logs']);
    }

    private function _isIgnoredMediaPath(string $path): bool
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        $segments = array_filter(explode('/', $path), 'strlen');
        $filename = strtolower((string) end($segments));
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $ignoredNames = [
            '.ds_store',
            'thumbs.db',
            'desktop.ini',
            '.htaccess',
            '.htpasswd',
            'index.html',
            'index.htm'
        ];
        $ignoredDirectories = [
            '_extension',
            '_import_tmp',
            'captcha',
            'logs',
            '.spotlight-v100',
            '.trashes',
            '__macosx'
        ];

        if (! $filename || in_array($filename, $ignoredNames) || in_array($extension, ['html', 'htm'])) {
            return true;
        }

        if (str_starts_with($filename, '._')) {
            return true;
        }

        foreach ($segments as $segment) {
            if (in_array(strtolower($segment), $ignoredDirectories)) {
                return true;
            }
        }

        return false;
    }

    private function _mimeType(string $path): string
    {
        try {
            return (new Storage($this->_storage))->filesystem()->mimeType($path);
        } catch (Throwable $e) {
            return $this->_mimeTypeFromExtension($path);
        }
    }

    private function _mimeTypeFromExtension(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'css' => 'text/css',
            'js' => 'text/javascript',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream'
        };
    }

    private function _fileSize(string $path): int
    {
        try {
            return (new Storage($this->_storage))->filesystem()->fileSize($path);
        } catch (Throwable $e) {
            return 0;
        }
    }

    private function _lastModified(string $path): int
    {
        try {
            return (new Storage($this->_storage))->filesystem()->lastModified($path);
        } catch (Throwable $e) {
            return time();
        }
    }

    private function _formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }

    private function _getIcon($directory = null, $filename = null)
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
