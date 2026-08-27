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

namespace Aksara\Modules\XHR\Controllers;

use Aksara\Laboratory\Core;
use Aksara\Libraries\Uploader as UploaderLibrary;

class Uploader extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->permission->mustAjax();
    }

    /**
     * List uploaded files & images in the resolved upload directory or render uploader modal view
     */
    public function index()
    {
        $uploadPath = $this->_resolveUploadPath($this->request->getPost('path'));

        if ('fetch' === service('request')->getPost('mode')) {
            return $this->_fetch($uploadPath);
        }

        $this->setTitle(phrase('Media & File Manager'))
            ->setIcon('mdi mdi-folder-multiple-image')
            ->setOutput(['uploadPath' => $uploadPath])
            ->modalSize('modal-lg')
            ->render();
    }

    /**
     * Centralized file & image upload handler
     */
    public function upload()
    {
        if ($this->validToken($this->request->getPost('_token'), ['xhr/uploader'])) {
            if (DEMO_MODE) {
                return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page());
            }

            // Determine upload file source
            $file = $this->request->getFile('file')
                ?: $this->request->getFile('image')
                ?: $this->request->getFile('upload');

            if (! $file || ! $file->getName()) {
                return throw_exception(400, ['file' => phrase('No file uploaded.')]);
            }

            if (! $file->isValid()) {
                $errorCode = $file->getError();
                $errorMsg = in_array($errorCode, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? phrase('The selected file size exceeds the maximum allocation')
                    : ($file->getErrorString() ?: phrase('No file uploaded.'));

                return throw_exception(400, ['file' => $errorMsg]);
            }

            $uploadPath = $this->_resolveUploadPath($this->request->getPost('path'));
            $ownerUploadPath = $this->_appendOwnerPath($uploadPath);

            $targetDirectory = UPLOAD_PATH . ('/' !== substr($ownerUploadPath, 0, 1) ? '/' : '') . $ownerUploadPath;

            $uploader = new UploaderLibrary();
            $filename = $uploader->upload($file, $targetDirectory, 'image');

            if ($filename) {
                $imageUrl = get_image($ownerUploadPath, $filename);

                return make_json([
                    'status' => 'success',
                    'success' => true,
                    'source' => $imageUrl,
                    'image' => $imageUrl,
                    'url' => $imageUrl,
                    'name' => $filename,
                    'file' => $filename
                ]);
            }

            return throw_exception(400, ['file' => $uploader->getErrorString() ?: phrase('Upload Error!')]);
        }

        return throw_exception(400, ['file' => phrase('Invalid security token.')]);
    }

    /**
     * Centralized file & image delete handler
     */
    public function delete()
    {
        if ($this->validToken($this->request->getPost('_token'), ['xhr/uploader'])) {
            if (DEMO_MODE) {
                return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page());
            }

            $source = $this->request->getPost('source');

            if (! $source || ! is_string($source)) {
                return throw_exception(404, phrase('File was not found.'));
            }

            $filename = $this->_resolveSourceFilename($source);

            if (empty($filename) || '.' === $filename || '..' === $filename || str_contains($filename, "\0")) {
                return throw_exception(404, phrase('File was not found.'));
            }

            $uploadPath = $this->_resolveUploadPath($this->request->getPost('path'));
            $ownerUploadPath = ($this->_canAccessAllUploads() ? $uploadPath : $this->_appendOwnerPath($uploadPath));

            $baseDir = UPLOAD_PATH . ('/' !== substr($ownerUploadPath, 0, 1) ? '/' : '') . $ownerUploadPath;
            $targetDir = realpath($baseDir);
            $targetFile = $targetDir ? realpath($targetDir . DIRECTORY_SEPARATOR . $filename) : null;

            // Strict containment check: target file must exist, be a regular file, and reside inside $targetDir
            if ($targetDir && $targetFile && is_file($targetFile) && str_starts_with($targetFile, $targetDir . DIRECTORY_SEPARATOR)) {
                $deleted = @unlink($targetFile);

                // Clean up thumbnails/icons if they exist
                $thumbPath = dirname($targetFile) . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . basename($filename);
                $iconPath = dirname($targetFile) . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . basename($filename);

                if (is_file($thumbPath)) {
                    @unlink($thumbPath);
                }
                if (is_file($iconPath)) {
                    @unlink($iconPath);
                }

                if ($deleted) {
                    return throw_exception(200, phrase('File was successfully removed.'));
                }
            }

            return throw_exception(404, phrase('File was not found.'));
        }

        return throw_exception(400, phrase('Invalid security token.'));
    }

    private function _fetch(string $uploadPath)
    {
        $canAccessAllUploads = $this->_canAccessAllUploads();
        $uploadPath = ($canAccessAllUploads ? $uploadPath : $this->_appendOwnerPath($uploadPath));
        $targetDir = FCPATH . UPLOAD_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadPath);

        if (! is_dir($targetDir)) {
            return make_json([
                'images' => [],
                'files' => [],
                'page' => 1,
                'limit' => 12,
                'total' => 0,
                'total_pages' => 0
            ]);
        }

        $query = $this->request->getPost('q');
        $sort = $this->request->getPost('sort') ?? 'newest';
        $page = max(1, (int) ($this->request->getPost('page') ?? 1));
        $limit = max(1, min(100, (int) ($this->request->getPost('limit') ?? 12)));

        $scannedFiles = $this->_scanFiles($targetDir, $canAccessAllUploads);
        $uploader = new UploaderLibrary();
        $allowedExtensions = array_merge($uploader->imageExtensions(), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip']);

        $items = [];

        foreach ($scannedFiles as $file => $filePath) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (! is_file($filePath) || ! in_array($ext, $allowedExtensions, true)) {
                continue;
            }

            $isImage = in_array($ext, $uploader->imageExtensions(), true);

            if ($isImage) {
                try {
                    $mime = function_exists('mime_content_type') ? mime_content_type($filePath) : '';
                    if (! $uploader->isSafeImage($filePath, $ext, ($mime ?: null), $allowedExtensions)) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }

            // Search filter
            if ($query && stripos($file, $query) === false) {
                continue;
            }

            $fileSize = filesize($filePath);
            $fileTime = filemtime($filePath);
            $directory = dirname($file);
            $mediaPath = '.' === $directory ? $uploadPath : trim($uploadPath . '/' . $directory, '/');
            $mediaName = basename($file);

            $items[] = [
                'name' => $file,
                'url' => get_image($uploadPath, $file),
                'thumb' => $isImage ? get_image($mediaPath, $mediaName, 'thumb') : null,
                'icon' => get_image($mediaPath, $mediaName, 'icon'),
                'is_image' => $isImage,
                'size' => $fileSize,
                'time' => $fileTime,
                'formatted_size' => number_format($fileSize / 1024, 2) . ' KB',
                'formatted_time' => date('Y-m-d H:i', $fileTime)
            ];
        }

        // Sort items
        usort($items, function ($a, $b) use ($sort) {
            if ('oldest' === $sort) {
                return $a['time'] <=> $b['time'];
            }
            if ('name_asc' === $sort) {
                return strcasecmp($a['name'], $b['name']);
            }
            if ('name_desc' === $sort) {
                return strcasecmp($b['name'], $a['name']);
            }

            return $b['time'] <=> $a['time'];
        });

        $total = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $limit, $limit);

        return make_json([
            'images' => $paginatedItems,
            'files' => $paginatedItems,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit)
        ]);
    }

    /**
     * Resolve and sanitize subfolder upload path to prevent path traversal
     */
    private function _resolveUploadPath(?string $customPath = null): string
    {
        $path = $customPath ?: 'media';

        // Sanitize: allow alphanumeric, hyphen, underscore, and slash only
        $path = preg_replace('/[^A-Za-z0-9\-\_\/]/', '', $path);
        $path = trim($path, '/');

        // Reject path traversal segments (.. or empty)
        $segments = array_filter(explode('/', $path), function ($segment) {
            return ! empty($segment) && '.' !== $segment && '..' !== $segment;
        });

        return implode('/', $segments) ?: 'media';
    }

    private function _appendOwnerPath(string $uploadPath): string
    {
        $userId = (int) get_userdata('user_id');

        if ($userId <= 0) {
            return $uploadPath;
        }

        $uploadPath = trim($uploadPath, '/');

        if (basename($uploadPath) === (string) $userId) {
            return $uploadPath;
        }

        return $uploadPath . '/' . $userId;
    }

    private function _canAccessAllUploads(): bool
    {
        return in_array((int) get_userdata('group_id'), [1, 2], true);
    }

    private function _scanFiles(string $targetDir, bool $recursive = false): array
    {
        $files = [];
        $excluded = ['.', '..', 'index.html', '.htaccess', 'thumbs', 'icons', 'placeholder.png'];

        if (! $recursive) {
            foreach (array_diff(scandir($targetDir) ?: [], $excluded) as $file) {
                $files[$file] = $targetDir . DIRECTORY_SEPARATOR . $file;
            }

            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($targetDir, \FilesystemIterator::SKIP_DOTS),
                function ($current) use ($excluded) {
                    return ! in_array($current->getFilename(), $excluded, true);
                }
            )
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($targetDir) + 1);
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $files[$relative] = $file->getPathname();
        }

        return $files;
    }

    private function _resolveSourceFilename(string $source): string
    {
        $source = parse_url($source, PHP_URL_PATH) ?: $source;
        $source = trim(str_replace('\\', '/', $source), '/');

        if (! $this->_canAccessAllUploads()) {
            return basename($source);
        }

        $source = preg_replace('/[^A-Za-z0-9\-\_\/\.]/', '', $source);
        $segments = array_filter(explode('/', $source), function ($segment) {
            return ! empty($segment) && '.' !== $segment && '..' !== $segment;
        });

        return implode('/', $segments);
    }
}
