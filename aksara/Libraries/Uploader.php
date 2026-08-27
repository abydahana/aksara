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

namespace Aksara\Libraries;

use Config\Mimes;
use Config\Services;
use Throwable;
use CodeIgniter\Files\FileSizeUnit;
use CodeIgniter\HTTP\Files\UploadedFile;

class Uploader
{
    private const RASTER_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function upload(UploadedFile $source, string $path, string $type = 'file'): string|false
    {
        $this->_error = '';
        $type = strtolower($type);

        if (! in_array($type, ['image', 'file'], true)) {
            $this->_error = phrase('The selected upload type is not supported.');

            return false;
        }

        if (! $source->isValid()) {
            $errorCode = $source->getError();

            if (in_array($errorCode, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                $this->_error = phrase('The selected file size exceeds the maximum allocation');
            } elseif (UPLOAD_ERR_NO_FILE === $errorCode) {
                $this->_error = phrase('No file uploaded.');
            } elseif (UPLOAD_ERR_PARTIAL === $errorCode) {
                $this->_error = phrase('The uploaded file was only partially uploaded.');
            } else {
                $this->_error = $source->getErrorString() ?: phrase('Failed to upload file.');
            }

            return false;
        }

        if ($source->hasMoved()) {
            $this->_error = phrase('The uploaded file has already been moved.');

            return false;
        }

        if ((float) $source->getSizeByMetricUnit(FileSizeUnit::MB) > MAX_UPLOAD_SIZE) {
            $this->_error = phrase('The selected file size exceeds the maximum allocation');

            return false;
        }

        if (! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            $this->_error = phrase('The upload folder is not writable.');

            return false;
        }

        return 'image' === $type
            ? $this->_saveImage($source, $path, IMAGE_FORMAT_ALLOWED)
            : $this->_saveFile($source, $path, DOCUMENT_FORMAT_ALLOWED);
    }

    public function getErrorString(): string
    {
        return $this->_error;
    }

    public function imageExtensions(array|string|null $extensions = null): array
    {
        $extensions = $this->_normalizeExtensions($extensions ?? IMAGE_FORMAT_ALLOWED);

        if (! $extensions) {
            $extensions = self::RASTER_IMAGE_EXTENSIONS;
        }

        return $extensions;
    }

    public function isSafeImage(string $filePath, string $extension, ?string $mimeType = null, array|string|null $allowedExtensions = null): bool
    {
        $extension = strtolower(trim($extension, '. '));
        $allowedExtensions = $this->imageExtensions($allowedExtensions);
        $processableExtensions = $this->_rasterImageExtensions($allowedExtensions);

        if (! is_file($filePath) || ! in_array($extension, $processableExtensions, true)) {
            return false;
        }

        if ($mimeType && ! in_array($mimeType, $this->_imageMimeTypes($processableExtensions), true)) {
            return false;
        }

        if (! getimagesize($filePath)) {
            return false;
        }

        return $this->_hasNoScriptPayload($filePath);
    }

    private string $_error = '';

    private function _saveImage(UploadedFile $source, string $path, array|string|null $allowedExtensions = null): string|false
    {
        $allowedExtensions = $this->imageExtensions($allowedExtensions);
        $processableExtensions = $this->_rasterImageExtensions($allowedExtensions);
        $extension = strtolower($source->guessExtension() ?: '');

        if (! $extension || ! in_array($extension, $processableExtensions, true)) {
            $this->_error = phrase('The selected file extension is not allowed to be uploaded.');

            return false;
        }

        if (! in_array($source->getMimeType(), $this->_imageMimeTypes($processableExtensions), true)) {
            $this->_error = phrase('The selected file format is not allowed to be uploaded.');

            return false;
        }

        if (! $this->isSafeImage($source->getTempName(), $extension, $source->getMimeType(), $processableExtensions)) {
            $this->_error = phrase('The selected image file is not valid.');

            return false;
        }

        if (! $this->_ensureDirectory($path)) {
            $this->_error = phrase('The upload folder is not writable.');

            return false;
        }

        $filename = $this->_randomFilename($extension);
        $target = $path . DIRECTORY_SEPARATOR . $filename;

        if (! $source->move($path, $filename)) {
            $this->_error = phrase('Failed to move uploaded file.');

            return false;
        }

        if (! $this->_sanitizeImage($target, $extension, $processableExtensions)) {
            if (is_file($target)) {
                unlink($target);
            }

            $this->_error = phrase('Unable to process the uploaded image.');

            return false;
        }

        resize_image($target);

        return $filename;
    }

    private function _saveFile(UploadedFile $source, string $path, array|string|null $allowedExtensions = null): string|false
    {
        $allowedExtensions = $this->_fileExtensions($allowedExtensions);
        $extension = strtolower($source->getExtension());

        if (! $extension || ! in_array($extension, $allowedExtensions, true)) {
            $this->_error = phrase('The selected file extension is not allowed to be uploaded.');

            return false;
        }

        if (! in_array($source->getMimeType(), $this->_fileMimeTypes($allowedExtensions), true)) {
            $this->_error = phrase('The selected file format is not allowed to be uploaded.');

            return false;
        }

        if (! $this->_isSafeFile($source->getPathName(), $extension, $source->getMimeType(), $allowedExtensions)) {
            $this->_error = phrase('The file is not allowed to be uploaded.');

            return false;
        }

        if (! $this->_ensureDirectory($path)) {
            $this->_error = phrase('The upload folder is not writable.');

            return false;
        }

        $filename = $source->getRandomName();

        if (! $source->move($path, $filename)) {
            $this->_error = phrase('Failed to move uploaded file.');

            return false;
        }

        return $filename;
    }

    private function _sanitizeImage(string $filePath, string $extension, array|string|null $allowedExtensions = null): bool
    {
        $extension = strtolower(trim($extension, '. '));
        $tempPath = dirname($filePath) . DIRECTORY_SEPARATOR . uniqid('image_', true) . '.' . $extension;

        try {
            Services::image('gd')
                ->withFile($filePath)
                ->save($tempPath);

            if (! $this->isSafeImage($tempPath, $extension, null, $allowedExtensions)) {
                unlink($tempPath);

                return false;
            }

            return rename($tempPath, $filePath);
        } catch (Throwable $e) {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }

            return false;
        }
    }

    private function _imageMimeTypes(array|string|null $extensions = null): array
    {
        return $this->_mimeTypes($this->imageExtensions($extensions));
    }

    private function _rasterImageExtensions(array|string|null $extensions = null): array
    {
        return array_values(array_intersect(
            $this->imageExtensions($extensions),
            self::RASTER_IMAGE_EXTENSIONS
        ));
    }

    private function _fileExtensions(array|string|null $extensions = null): array
    {
        return $this->_normalizeExtensions($extensions ?? DOCUMENT_FORMAT_ALLOWED);
    }

    private function _fileMimeTypes(array|string|null $extensions = null): array
    {
        return $this->_mimeTypes($this->_fileExtensions($extensions));
    }

    private function _isSafeFile(string $filePath, string $extension, ?string $mimeType = null, array|string|null $allowedExtensions = null): bool
    {
        $extension = strtolower(trim($extension, '. '));
        $allowedExtensions = $this->_fileExtensions($allowedExtensions);

        if (! is_file($filePath) || ! in_array($extension, $allowedExtensions, true)) {
            return false;
        }

        if ($mimeType && ! in_array($mimeType, $this->_fileMimeTypes($allowedExtensions), true)) {
            return false;
        }

        return $this->_hasNoScriptPayload($filePath);
    }

    private function _mimeTypes(array $extensions): array
    {
        $mimeTypes = [];

        foreach ($extensions as $extension) {
            foreach ((array) Mimes::guessTypeFromExtension($extension) as $type) {
                if ($type) {
                    $mimeTypes[] = $type;
                }
            }
        }

        return array_values(array_unique($mimeTypes));
    }

    private function _normalizeExtensions(array|string $extensions): array
    {
        if (is_string($extensions)) {
            $extensions = explode(',', $extensions);
        }

        return array_values(array_filter(array_map(static fn ($extension) => strtolower(trim($extension, '. ')), $extensions)));
    }

    private function _hasNoScriptPayload(string $filePath): bool
    {
        $content = file_get_contents($filePath);

        if (false === $content) {
            return false;
        }

        return ! preg_match('/<\?php\b|<\?=\s*[\$a-zA-Z0-9_\'"\`\(\[\{\\\\]|<\?\s*(?:eval|exec|system|passthru|shell_exec|base64_decode|gzinflate|str_rot13|include|require|assert|call_user_func|\$[a-zA-Z_]|function\b|class\b|echo\b|print\b|die\b|exit\b|\/\*|\/\/|#)|<script\b/i', $content);
    }

    private function _ensureDirectory(string $path): bool
    {
        if (is_dir($path)) {
            return is_writable($path);
        }

        if (! mkdir($path, 0755, true)) {
            return false;
        }

        if (is_file(UPLOAD_PATH . '/placeholder.png')) {
            copy(UPLOAD_PATH . '/placeholder.png', $path . DIRECTORY_SEPARATOR . 'placeholder.png');
        }

        return is_writable($path);
    }

    private function _randomFilename(string $extension): string
    {
        return time() . '_' . bin2hex(random_bytes(10)) . '.' . strtolower(trim($extension, '. '));
    }
}
