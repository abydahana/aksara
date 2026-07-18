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

namespace Aksara\Modules\Themes\Controllers;

use Aksara\Laboratory\Core;
use CodeIgniter\Files\File;

class Themes extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $uriString = uri_string();
        $extension = strtolower(pathinfo($uriString, PATHINFO_EXTENSION));

        // Security: Block sensitive file types
        $blocked = ['php', 'twig', 'json', 'env', 'sql', 'lock', 'log'];
        if (in_array($extension, $blocked) || empty($extension)) {
            return $this->_error404();
        }

        // Resolve the absolute path
        $targetFile = ROOTPATH . $uriString;
        $realPath = realpath($targetFile);

        // Security: Path Validation
        $themesDir = realpath(ROOTPATH . 'themes');

        if (! $realPath || ! is_file($realPath) || strpos($realPath, $themesDir) !== 0) {
            return $this->_error404();
        }

        $this->_serveAsset($realPath);
    }

    private function _error404()
    {
        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }

    private function _serveAsset(string $realPath): void
    {
        $mimeType = $this->_guessMimeType($realPath);

        // Bersihkan output yang mungkin sudah terkirim
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
