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
        $extension = strtolower(pathinfo(uri_string(), PATHINFO_EXTENSION));

        if (is_file(ROOTPATH . uri_string()) && ! in_array($extension, ['php', 'twig'])) {
            helper('download');

            return force_download(basename(uri_string()), file_get_contents(ROOTPATH . uri_string()), true);
        }

        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }
}
