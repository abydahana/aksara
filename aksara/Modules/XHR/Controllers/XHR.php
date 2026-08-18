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

class XHR extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return throw_exception(404, phrase('The page you requested does not exist or has already been archived.'));
    }

    public function settings()
    {
        $output = [];

        if ($this->request->getPost('hide_greeting')) {
            set_userdata('hide_greeting', true);

            $output['hide_greeting'] = true;
        }

        return make_json($output);
    }

    public function themeToggle()
    {
        $theme = $this->request->getPost('theme') ?? $this->request->getGet('theme');

        if (in_array($theme, ['dark', 'light'], true)) {
            set_userdata('app_theme', $theme);
        }

        return make_json([
            'status' => 200,
            'theme' => $theme
        ]);
    }

    public function setYear()
    {
        $year = $this->request->getGet('year');

        if ($year) {
            set_userdata('year', $year);
        }

        return throw_exception(301, phrase('Year changed successfully.'), $this->request->getServer('HTTP_REFERER'), true);
    }

    public function captcha()
    {
        // Load captcha helper
        helper('captcha');

        return make_json(generate_captcha());
    }

    public function columns()
    {
        $path = $this->request->getPost('path');
        $columns = $this->request->getPost('columns') ?? $this->request->getGet('columns') ?? $this->request->getPost('hidden_cols') ?? $this->request->getGet('hidden_cols');

        // Clean path (strip leading/trailing slashes and domain/query string)
        $path = trim(parse_url($path, PHP_URL_PATH) ?? '', '/');

        $hiddenCols = [];
        if (is_array($columns)) {
            $hiddenCols = array_values(array_filter(array_map('trim', $columns)));
        } elseif (is_string($columns) && strlen(trim($columns))) {
            $hiddenCols = array_values(array_filter(array_map('trim', explode(',', $columns))));
        }

        // Store hidden columns in PHP session for this path
        $sessionKey = 'hidden_cols_' . md5($path);

        set_userdata($sessionKey, $hiddenCols);

        return make_json([
            'status' => 200,
            'path' => $path,
            'hidden_cols' => $hiddenCols
        ]);
    }
}
