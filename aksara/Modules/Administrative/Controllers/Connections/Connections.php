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

namespace Aksara\Modules\Administrative\Controllers\Connections;

use Throwable;
use Config\Services;
use Aksara\Laboratory\Core;

class Connections extends Core
{
    private string $_table = 'app_connections';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');
    }

    public function index()
    {
        if ($this->request->getPost('year')) {
            $this->setValidation('year', 'valid_year');
        }

        $this->setTitle(phrase('Third Party Connection'))
        ->setIcon('mdi mdi-power-plug')
        ->unsetColumn('hostname, port, username, password, database_name')
        ->unsetField('id')

        ->addButton('connect', phrase('Check Connection'), 'btn btn-success --xhr show-progress', 'mdi mdi-sync', ['id' => 'id'])

        ->setField([
            'description' => 'textarea',
            'username' => 'encryption',
            'password' => 'encryption',
            'port' => 'integer',
            'status' => 'boolean'
        ])
        ->setField(
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
        ->setRelation(
            'year',
            'app_years.year',
            '{{ app_years.year AS year_label }}',
            [
                'app_years.status' => 1
            ]
        )
        ->fieldPosition([
            'database_driver' => 2,
            'hostname' => 2,
            'port' => 2,
            'username' => 2,
            'password' => 2,
            'database_name' => 2,
            'status' => 2
        ])
        ->mergeField('hostname, port')
        ->fieldSize([
            'hostname' => 'col-sm-8',
            'port' => 'col-sm-4'
        ])
        ->setValidation([
            'name' => 'required',
            'database_driver' => 'required|in_list[MySQLi,SQLSRV,Postgre,SQLite3,OCI8]',
            'hostname' => 'required',
            'port' => 'required|integer',
            'username' => 'required',
            'database_name' => 'required'
        ])
        ->setAlias([
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
        $this->permission->mustAjax();

        $connectionId = $this->model->getWhere(
            $this->_table,
            [
                'id' => $this->request->getGet('id') ?? 0
            ],
            1
        )
        ->row('id');

        if (! $connectionId) {
            return throw_exception(404, phrase('The database connection is not found!'));
        }

        try {
            $connector = $this->model->databaseConfig($connectionId);

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
