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

namespace Aksara\Modules\Dashboard\Controllers;

use Aksara\Laboratory\Core;
use Aksara\Modules\Administrative\Controllers\Updater\Updater;
use DateTime;
use DateInterval;

class Dashboard extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');

        $this->set_method('index');

        if ('fetch_information' == $this->request->getPost('request')) {
            return $this->_fetch_information();
        }
    }

    public function index()
    {
        if (get_userdata('group_id') > 2) {
            $this->set_template('index', 'index_subscriber');

            $this->set_output([
                'announcements' => $this->_announcements()
            ]);
        } elseif (get_userdata('group_id') > 1) {
            $this->set_template('index', 'index_technical');

            $this->set_output([
                'card' => $this->_card(),
                'visitors' => $this->_visitors(),
                'recent_signed' => $this->_recent_signed(),
                'system_language' => $this->_system_language(),
                'announcements' => $this->_announcements()
            ]);
        } else {
            $this->set_output([
                'card' => $this->_card(),
                'permission' => [
                    'uploads' => (is_dir(FCPATH . UPLOAD_PATH) && is_writable(FCPATH . UPLOAD_PATH) ? true : false),
                    'writable' => (is_dir(WRITEPATH) && is_writable(WRITEPATH) ? true : false),
                ],
                'visitors' => $this->_visitors(),
                'recent_signed' => $this->_recent_signed(),
                'system_language' => $this->_system_language(),
                'announcements' => $this->_announcements()
            ]);
        }

        $this->set_title(phrase('Dashboard'))
        ->set_icon('mdi mdi-monitor-dashboard')

        ->render();
    }

    private function _card()
    {
        $blogs = $this->model->get_where(
            'blogs',
            [
            ]
        )
        ->num_rows();

        $pages = $this->model->get_where(
            'pages',
            [
            ]
        )
        ->num_rows();

        $galleries = $this->model->get_where(
            'galleries',
            [
            ]
        )
        ->num_rows();

        $users = $this->model->get_where(
            'app__users',
            [
            ]
        )
        ->num_rows();

        return [
            'blogs' => ($blogs ? $blogs : 0),
            'pages' => ($pages ? $pages : 0),
            'galleries' => ($galleries ? $galleries : 0),
            'users' => ($users ? $users : 0)
        ];
    }

    private function _visitors()
    {
        $visitors = $this->model->get_where(
            'app__log_visitors',
            [
                'DATE(timestamp) >= ' => date('Y-m-d', strtotime('-6 days')),
                'DATE(timestamp) <= ' => date('Y-m-d')
            ]
        )
        ->result();

        $output = [];

        foreach (range(1, 7) as $key => $val) {
            $date = new DateTime();
            $date->add(new DateInterval('P' . $val . 'D'));

            // Translate day name
            $day = phrase($date->format('l'));

            // Push day name to visits
            $output['days'][] = $day;
            $output['visits'][$day] = 0;
        }

        $browsers = [
            'chrome' => 0,
            'firefox' => 0,
            'safari' => 0,
            'edge' => 0,
            'opera' => 0,
            'explorer' => 0,
            'unknown' => 0
        ];

        if ($visitors) {
            foreach ($visitors as $key => $val) {
                // Translate day name
                $day = phrase(date('l', strtotime($val->timestamp)));

                // Increase number of visits based with its day name
                $output['visits'][$day]++;

                if (stripos($val->browser, 'chrome') !== false) {
                    $browsers['chrome']++;
                } elseif (stripos($val->browser, 'firefox') !== false) {
                    $browsers['firefox']++;
                } elseif (stripos($val->browser, 'safari') !== false) {
                    $browsers['safari']++;
                } elseif (stripos($val->browser, 'edge') !== false) {
                    $browsers['edge']++;
                } elseif (stripos($val->browser, 'opera') !== false) {
                    $browsers['opera']++;
                } elseif (stripos($val->browser, 'explorer') !== false) {
                    $browsers['explorer']++;
                } else {
                    $browsers['unknown']++;
                }
            }
        }

        arsort($browsers);

        return [
            'categories' => $output['days'],
            'visits' => array_values($output['visits']),
            'browsers' => $browsers
        ];
    }

    private function _recent_signed()
    {
        $query = $this->model->select('
            app__users.user_id,
            app__users.username,
            app__users.first_name,
            app__users.last_name,
            app__users.photo,
            app__groups.group_name
        ')
        ->join(
            'app__groups',
            'app__groups.group_id = app__users.group_id'
        )
        ->order_by('last_login', 'DESC')
        ->get_where(
            'app__users',
            [
                'app__users.status' => 1
            ],
            7
        )
        ->result();

        return $query;
    }

    private function _fetch_information()
    {
        $updater = false;

        if (get_setting('update_check')) {
            $updater = Updater::ping_upstream();
        }

        return make_json(
            [
                'update_available' => $updater
            ]
        );
    }

    private function _system_language()
    {
        $language_id = get_setting('app_language');

        $query = $this->model->select('language')->get_where(
            'app__languages',
            [
                'id' => $language_id
            ]
        )
        ->row('language');

        return ($query ? $query : phrase('Default'));
    }

    private function _announcements()
    {
        $query = $this->model->order_by('end_date', 'DESC')
        ->order_by('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->get_where(
            'announcements',
            [
                'placement' => 1,
                'status' => 1,
                'start_date <= ' => date('Y-m-d'),
                'end_date >= ' => date('Y-m-d')
            ],
            5
        )
        ->result();

        return $query;
    }
}
