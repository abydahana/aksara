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

namespace Aksara\Modules\Modules\Controllers;

use Aksara\Laboratory\Core;
use CodeIgniter\Files\File;

class Modules extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $uriString = uri_string();
        $extension = strtolower(pathinfo($uriString, PATHINFO_EXTENSION));

        // Security Check: Block sensitive files and direct code execution
        $blockedExtensions = ['php', 'twig', 'env', 'json', 'lock', 'sql', 'log'];
        if (in_array($extension, $blockedExtensions) || empty($extension)) {
            return $this->_error404();
        }

        // Extract relative module path (e.g. "XHR/assets/js/purify.min.js")
        $relativeModulePath = preg_replace('#^.*modules/#i', '', $uriString);

        // Parse module name and subpath
        $parts = explode('/', str_replace('\\', '/', $relativeModulePath), 2);
        $moduleName = $parts[0] ?? '';
        $subPath = $parts[1] ?? '';

        // Define search locations (Priority: custom modules, core Aksara Modules, APPPATH Modules)
        $locations = [
            ROOTPATH . $uriString,
            ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeModulePath),
            ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeModulePath),
            APPPATH . 'Modules' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeModulePath)
        ];

        // Case-insensitive directory lookup for Linux VPS
        if ($moduleName) {
            $baseDirs = [
                ROOTPATH . 'modules',
                ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules',
                APPPATH . 'Modules'
            ];

            foreach ($baseDirs as $baseDir) {
                if (! is_dir($baseDir)) {
                    continue;
                }

                if ($dh = @opendir($baseDir)) {
                    while (false !== ($dir = readdir($dh))) {
                        if ('.' !== $dir && '..' !== $dir && strcasecmp($dir, $moduleName) === 0) {
                            $locations[] = $baseDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subPath);
                            break;
                        }
                    }
                    closedir($dh);
                }
            }
        }

        foreach ($locations as $path) {
            // Resolve real path to prevent directory traversal attacks (e.g. ../../)
            $realPath = realpath($path);

            if ($realPath && is_file($realPath)) {
                // Ensure the file is actually inside ROOTPATH or APPPATH for safety
                $rootReal = realpath(ROOTPATH);
                $appReal = realpath(APPPATH);

                if (($rootReal && strpos($realPath, $rootReal) === 0) || ($appReal && strpos($realPath, $appReal) === 0)) {
                    $this->_serveAsset($realPath);
                }
            }
        }

        return $this->_error404();
    }

    private function _error404()
    {
        return throw_exception(404, phrase('The page you requested does not exist or has already been archived.'), base_url());
    }

    private function _serveAsset(string $realPath): void
    {
        $mimeType = $this->_guessMimeType($realPath);

        // Clean output buffer that might have already been sent
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realPath));
        header(
            'Content-Disposition: inline; filename="' .
            addslashes(basename($realPath)) .
            '"'
        );
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=3600');

        readfile($realPath);
        exit;
    }

    private function _guessMimeType(string $realPath): string
    {
        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'mjs' => 'application/javascript',
            'json' => 'application/json',
            'map' => 'application/json',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'ico' => 'image/x-icon',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        if (isset($mimeTypes[$extension])) {
            return $mimeTypes[$extension];
        }

        $file = new File($realPath);

        return $file->getMimeType() ?: 'application/octet-stream';
    }
}
