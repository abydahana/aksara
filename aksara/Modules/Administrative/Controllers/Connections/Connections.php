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

namespace Aksara\Modules\Administrative\Controllers\Connections;

use Throwable;
use Config\Services;
use Aksara\Laboratory\Core;

class Connections extends Core
{
    private $_table = 'app__connections';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');
    }

    public function index()
    {
        if ($this->request->getPost('year')) {
            $this->set_validation('year', 'valid_year');
        }

        $this->set_title(phrase('Third Party Connection'))
        ->set_icon('mdi mdi-power-plug')
        ->unset_column('hostname, port, username, password, database_name')
        ->unset_field('id')

        ->add_button('connect', phrase('Check Connection'), 'btn btn-success --xhr show-progress', 'mdi mdi-sync', ['id' => 'id'])

        ->set_field([
            'description' => 'textarea',
            'username' => 'encryption',
            'password' => 'encryption',
            'port' => 'integer',
            'status' => 'boolean'
        ])
        ->set_field(
            'database_driver',
            'select',
            [
                'MySQLi' => 'MySQLi',
                'SQLSRV' => 'Microsoft SQL Server',
                'Postgre' => 'PostgreSQL',
                'SQLite3' => 'SQLite',
                'OCI8' => 'Oracle (OCI8)'
            ]
        )
        ->set_relation(
            'year',
            'app__years.year',
            '{{ app__years.year AS year_label }}',
            [
                'app__years.status' => 1
            ]
        )
        ->field_position([
            'database_driver' => 2,
            'hostname' => 2,
            'port' => 2,
            'username' => 2,
            'password' => 2,
            'database_name' => 2,
            'status' => 2
        ])
        ->merge_field('hostname, port')
        ->field_size([
            'hostname' => 'col-sm-8',
            'port' => 'col-sm-4'
        ])
        ->set_validation([
            'name' => 'required',
            'database_driver' => 'required|in_list[MySQLi,SQLSRV,Postgre,SQLite3,OCI8]',
            'hostname' => 'required',
            'port' => 'required|integer',
            'username' => 'required',
            'database_name' => 'required'
        ])
        ->set_alias([
            'year' => phrase('Year'),
            'year_label' => phrase('Year'),
            'name' => phrase('Connection Name'),
            'description' => phrase('Description'),
            'database_driver' => phrase('Database Driver'),
            'username' => phrase('Username'),
            'password' => phrase('Password'),
            'database_name' => phrase('Database Name')
        ])
        ->render($this->_table);
    }

    public function connect()
    {
        $this->permission->must_ajax();

        $query = $this->model->get_where(
            $this->_table,
            [
                'id' => $this->request->getGet('id')
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The database connection is not found!'));
        }

        try {
            $encrypter = Services::encrypter();

            // Try to decrypting the parameter
            $connection = [
                'DBDriver' => $query->database_driver,
                'hostname' => $query->hostname,
                'username' => $encrypter->decrypt(base64_decode($query->username)),
                'password' => $encrypter->decrypt(base64_decode($query->password)),
                'database' => $query->database_name,
                'DBDebug' => (ENVIRONMENT !== 'production')
            ];

            if ($query->port) {
                $connection['port'] = $query->port;
            }

            $connector = $this->model->database_config($connection);

            if (is_array($connector) && isset($connector['code'])) {
                return throw_exception(403, $connector['message']);
            }

            return throw_exception(200, phrase('The database was successfully connected!'));
        } catch (Throwable $e) {
            // Decrypt error
            return throw_exception(403, $e->getMessage());
        }
    }
}
