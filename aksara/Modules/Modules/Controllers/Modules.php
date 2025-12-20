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

namespace Aksara\Modules\Modules\Controllers;

use Aksara\Laboratory\Core;

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

        // Define search locations (Priority: ROOT then APPPATH)
        $locations = [
            ROOTPATH . $uriString,
            APPPATH . 'Modules' . DIRECTORY_SEPARATOR . preg_replace('#^.*modules/#', '', $uriString)
        ];

        foreach ($locations as $path) {
            // Resolve real path to prevent directory traversal attacks (e.g. ../../)
            $realPath = realpath($path);

            if ($realPath && is_file($realPath)) {
                // Ensure the file is actually inside ROOTPATH or APPPATH for safety
                if (strpos($realPath, realpath(ROOTPATH)) === 0 || strpos($realPath, realpath(APPPATH)) === 0) {
                    return $this->response->download($realPath, null, true)->send();
                }
            }
        }

        return $this->_error404();
    }

    private function _error404()
    {
        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }
}
