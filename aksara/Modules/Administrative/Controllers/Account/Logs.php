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

class Logs extends \Aksara\Laboratory\Core
{
    private $_table = 'app__log_activities';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->parent_module('administrative/account');

        $this->set_permission();
        $this->set_theme('backend');
        $this->set_method('index');

        if (service('request')->getPost('fetch') == 'ip-info') {
            return $this->_get_ip_info(service('request')->getPost('ip_address'), service('request')->getPost('key'));
        }
    }

    public function index()
    {
        $this->set_title(phrase('Login Activities'))
        ->set_icon('mdi mdi-information-outline')

        ->set_output([
            'logs' => $this->_get_logs()
        ])

        ->render();
    }

    public function kick()
    {
        if (service('request')->getGet('session')) {
            if (file_exists(WRITEPATH . 'session/' . service('request')->getGet('session'))) {
                unlink(WRITEPATH . 'session/' . service('request')->getGet('session'));
            }

            $this->model->update($this->_table, ['session_id' => ''], ['session_id' => service('request')->getGet('session')]);

            return throw_exception(301, phrase('The device was successfully kicked'), current_page('../', ['session' => null]));
        }

        return throw_exception(404, phrase('Unable to kick the selected device'), current_page('../', ['session' => null]));
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
        ->group_by('ip_address')
        ->order_by('timestamp', 'DESC')
        ->get_where(
            $this->_table,
            [
                'user_id' => get_userdata('user_id'),
                'timestamp >= ' => date('Y-m-d H:i:s', strtotime('-1 month'))
            ]
        )
        ->result();

        $output = [];

        foreach ($query as $key => $val) {
            $output[$val->platform][] = $val;
        }

        return $output;
    }

    private function _get_ip_info($ip_address = null, $key = null)
    {
        if (! $ip_address || '::1' == $ip_address) {
            return false;
        }

        $information = file_get_contents('https://www.aksaracms.com/ip/' . $ip_address);

        return make_json(json_decode($information));
    }
}
