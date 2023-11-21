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

class Modules extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $segments = implode(DIRECTORY_SEPARATOR, service('uri')->getSegments());
        $segments = str_ireplace('modules' . DIRECTORY_SEPARATOR . 'aksara', 'aksara' . DIRECTORY_SEPARATOR . 'Modules', $segments);

        if ($segments) {
            if ('php' == strtolower(pathinfo($segments, PATHINFO_EXTENSION)) || ! is_file(ROOTPATH . dirname($segments) . DIRECTORY_SEPARATOR . basename($segments)) || ! file_exists(ROOTPATH . dirname($segments) . DIRECTORY_SEPARATOR . basename($segments))) {
                return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url('not_found'));
            }

            $path = ROOTPATH . dirname($segments);
            $filename = basename($segments);
            $mime = mime_content_type($path . DIRECTORY_SEPARATOR . $filename);

            if ('css' == strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                $mime = 'text/css';
            } elseif ('js' == strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                $mime = 'text/javascript';
            }

            service('response')->setContentType($mime);
            service('response')->setBody(file_get_contents($path . DIRECTORY_SEPARATOR . $filename));

            return service('response')->send();
        }
    }
}
