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

namespace Aksara\Modules\Administrative\Controllers\Account;

use Aksara\Laboratory\Core;

class Logs extends Core
{
    private $_table = 'app__log_activities';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->parentModule('administrative/account');

        $this->setPermission();
        $this->setTheme('backend');
        $this->setMethod('index');

        if ($this->request->getPost('fetch') == 'ip-info') {
            return $this->_get_ip_info($this->request->getPost('ip_address'), $this->request->getPost('key'));
        }
    }

    public function index()
    {
        $this->setTitle(phrase('Login Activities'))
        ->setIcon('mdi mdi-information-outline')

        ->setOutput([
            'logs' => $this->_get_logs()
        ])

        ->render();
    }

    public function kick()
    {
        if ($this->request->getGet('session')) {
            if ($this->request->getGet('session') && file_exists(WRITEPATH . 'session/' . $this->request->getGet('session'))) {
                unlink(WRITEPATH . 'session/' . $this->request->getGet('session'));
            }

            // Update table to skip getting session_id on next execution
            $this->model->update($this->_table, ['session_id' => ''], ['session_id' => $this->request->getGet('session')]);

            return throw_exception(301, phrase('The device was successfully kicked.'), current_page('../', ['session' => null]));
        }

        return throw_exception(404, phrase('Unable to kick the selected device.'), current_page('../', ['session' => null]));
    }

    public function _get_logs()
    {
        $query = $this->model->select('
            session_id,
            ip_address,
            browser,
            platform,
            timestamp
        ')
        ->orderBy('timestamp', 'DESC')
        ->getWhere(
            $this->_table,
            [
                'user_id' => get_userdata('user_id'),
                'timestamp >= ' => date('Y-m-d H:i:s', strtotime('-1 month'))
            ]
        )
        ->result();

        $output = [];
        $ipAddresses = [];

        foreach ($query as $key => $val) {
            // Unique by IP Address
            if (in_array($val->ip_address, $ipAddresses, true)) {
                continue;
            }

            $output[$val->platform][] = $val;

            // Add IP Address into collections
            $ipAddresses[] = $val->ip_address;
        }

        return $output;
    }

    private function _get_ip_info($ipAddress = null, $key = null)
    {
        if (! $ipAddress || '::1' === $ipAddress) {
            return false;
        }

        $information = file_get_contents('https://www.aksaracms.com/ip/' . $ipAddress);

        return make_json(json_decode($information));
    }
}
