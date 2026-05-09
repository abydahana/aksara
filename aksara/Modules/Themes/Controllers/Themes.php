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

namespace Aksara\Modules\Themes\Controllers;

use Aksara\Laboratory\Core;

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

        // Serve file
        return $this->response->download($realPath, null, true)->setFileName(basename($realPath))->send();
    }

    private function _error404()
    {
        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }
}
