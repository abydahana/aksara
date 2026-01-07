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

use Aksara\Laboratory\Core;
use Throwable;

class Ftp extends Core
{
    private $_table = 'app__ftp';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->searchable(false);

        $this->setMethod('update');
        $this->permitUpsert();
    }

    public function index()
    {
        $this->setTitle(phrase('FTP Configuration'))
        ->setIcon('mdi mdi-console-network')
        ->unsetField('site_id')
        ->setField([
            'port' => 'number',
            'password' => 'encryption'
        ])
        ->setValidation([
            'hostname' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required'
        ])
        ->setDefault([
            'site_id' => get_setting('id')
        ])
        ->where([
            'site_id' => get_setting('id')
        ])
        ->mergeField('hostname, port')
        ->mergeField('username, password')
        ->fieldSize([
            'hostname' => 'col-md-9',
            'port' => 'col-md-3'
        ])
        ->render($this->_table);
    }

    public function beforeUpdate()
    {
        if ($this->request->getPost('checking') && $this->request->getPost('hostname') && $this->request->getPost('port')) {
            try {
                // Try to connect to FTP
                $connection = ftp_connect($this->request->getPost('hostname'), $this->request->getPost('port'), 10);

                if (! $connection || ! ftp_login($connection, $this->request->getPost('username'), $this->request->getPost('password'))) {
                    return throw_exception(400, ['hostname' => phrase('Unable to connect to the FTP using the provided configuration.')]);
                }
            } catch (Throwable $e) {
                throw_exception(400, ['hostname' => $e->getMessage()]);
            }
        }
    }
}
