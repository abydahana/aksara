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
        // Handle POST Content-Length overflow (exceeding php.ini post_max_size)
        if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > 0) {
            return throw_exception(400, ['file' => phrase('The selected file size exceeds the maximum allocation')]);
        }

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

            // Resolve subfolder path safely
            $uploadPath = $this->_resolveUploadPath($this->request->getPost('path'));

            $targetDirectory = UPLOAD_PATH . ('/' !== substr($uploadPath, 0, 1) ? '/' : '') . $uploadPath;

            $uploader = new UploaderLibrary();
            $filename = $uploader->upload($file, $targetDirectory, 'image');

            if ($filename) {
                $imageUrl = get_image($uploadPath, $filename);

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

            $filename = basename($source);

            if (empty($filename) || '.' === $filename || '..' === $filename || str_contains($filename, "\0")) {
                return throw_exception(404, phrase('File was not found.'));
            }

            // Resolve upload path safely
            $uploadPath = $this->_resolveUploadPath($this->request->getPost('path'));

            $baseDir = UPLOAD_PATH . ('/' !== substr($uploadPath, 0, 1) ? '/' : '') . $uploadPath;
            $targetDir = realpath($baseDir);
            $targetFile = $targetDir ? realpath($targetDir . DIRECTORY_SEPARATOR . $filename) : null;

            // Strict containment check: target file must exist, be a regular file, and reside inside $targetDir
            if ($targetDir && $targetFile && is_file($targetFile) && str_starts_with($targetFile, $targetDir . DIRECTORY_SEPARATOR)) {
                $deleted = @unlink($targetFile);

                // Clean up thumbnails/icons if they exist
                $thumbPath = $targetDir . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $filename;
                $iconPath = $targetDir . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $filename;

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

        $scannedFiles = array_diff(scandir($targetDir) ?: [], ['.', '..', 'index.html', '.htaccess', 'thumbs', 'icons', 'placeholder.png']);
        $uploader = new UploaderLibrary();
        $allowedExtensions = array_merge($uploader->imageExtensions(), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip']);

        $items = [];

        foreach ($scannedFiles as $file) {
            $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;
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

            $items[] = [
                'name' => $file,
                'url' => get_image($uploadPath, $file),
                'thumb' => $isImage ? get_image($uploadPath, $file, 'thumb') : null,
                'icon' => get_image($uploadPath, $file, 'icon'),
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
}
