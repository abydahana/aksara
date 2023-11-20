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

namespace Aksara\Modules\Addons\Controllers;

class Ftp extends \Aksara\Laboratory\Core
{
    private $_table = 'app__ftp';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->searchable(false);

        $this->set_method('update');
        $this->insert_on_update_fail();
    }

    public function index()
    {
        if (1 == service('request')->getPost('checking') && service('request')->getPost('hostname') && service('request')->getPost('port')) {
            $this->_connection_check();
        }

        $this->set_title(phrase('FTP Configuration'))
        ->set_icon('mdi mdi-console-network')
        ->unset_field('site_id')
        ->set_field([
            'port' => 'number',
            'password' => 'encryption'
        ])
        ->set_validation([
            'hostname' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required'
        ])
        ->set_default([
            'site_id' => get_setting('id')
        ])
        ->where([
            'site_id' => get_setting('id')
        ])
        ->merge_field('hostname, port')
        ->merge_field('username, password')
        ->field_size([
            'hostname' => 'col-md-9',
            'port' => 'col-md-3'
        ])
        ->render($this->_table);
    }

    private function _connection_check()
    {
        try {
            // Try to connect to FTP
            $connection = ftp_connect(service('request')->getPost('hostname'), service('request')->getPost('port'), 10);

            if (! $connection) {
                // Try to login to FTP
                $login = ftp_login($connection, service('request')->getPost('username'), service('request')->getPost('password'));
            }
        } catch(\Throwable $e) {
            throw_exception(400, ['hostname' => $e->getMessage()]);
        }
    }
}
