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

namespace Aksara\Modules\APIs\Controllers;

use Aksara\Laboratory\Core;

class Services extends Core
{
    private string $_table = 'app_rest_clients';
    private ?int $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->_primary = $this->request->getGet('id');
    }

    public function index()
    {
        $this->setTitle(phrase('Manage Services'))
        ->setIcon('mdi mdi-link-variant')
        ->unsetColumn('id, method, ip_range')
        ->unsetField('id')
        ->unsetView('id')
        ->setField([
            'description' => 'textarea',
            'ip_range' => 'textarea',
            'status' => 'boolean'
        ])
        ->setField(
            'method',
            'checkbox',
            [
                'GET' => 'GET ',
                'POST' => 'POST ',
                'DELETE' => 'DELETE '
            ]
        )
        ->defaultValue('api_key', $this->_apiKeyGenerator())
        ->mergeField('valid_until, status')
        ->setValidation([
            'title' => 'required|string|max_length[64]|unique[' . $this->_table . '.title.id.' . $this->_primary . ']',
            'description' => 'required|string',
            'api_key' => 'required|alpha_numeric|min_length[32]',
            'method' => 'required|in_list[GET,POST,DELETE]',
            'valid_until' => 'required|valid_date',
            'status' => 'boolean'
        ])

        ->setAlias([
            'title' => phrase('Title'),
            'description' => phrase('Description'),
            'api_key' => phrase('API Key'),
            'ip_range' => phrase('IP Range'),
            'method' => phrase('Request Method'),
            'valid_until' => phrase('Valid Until'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }

    private function _apiKeyGenerator($length = 32)
    {
        $characters = '0123456789ABCDEF';

        $char_length = strlen($characters);
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $output .= $characters[rand(0, $char_length - 1)];
        }

        $exist = $this->model->select('
            api_key
        ')
        ->getWhere(
            $this->_table,
            [
                'api_key' => $output
            ],
            1
        )
        ->row('api_key');

        if ($exist) {
            $this->_apiKeyGenerator();
        }

        return $output;
    }
}
