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

namespace Aksara\Modules\Administrative\Controllers\Logs;

ini_set('memory_limit', -1);

class Activities extends \Aksara\Laboratory\Core
{
    private $_table = 'app__log_activities';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('create, update, clone');
    }

    public function index()
    {
        $this->set_title(phrase('Log Activities'))
        ->set_icon('mdi mdi-information-outline')
        ->set_primary('id')
        ->unset_column('id, user_id, browser, session_id, query')
        ->unset_view('id, user_id, session_id, query')
        ->add_toolbar('truncate', phrase('Clear Logs'), 'btn-primary --open-delete-confirm', 'mdi mdi-delete-empty')
        ->set_field('timestamp', 'current_timestamp')
        ->set_field('first_name', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->set_field('path', 'hyperlink', '{{ path }}/{{ method }}', '{{ query }}')
        ->column_order('first_name')
        ->view_order('first_name')
        ->select('app__users.first_name')
        ->join('app__users', 'app__users.user_id = ' . $this->_table . '.user_id')
        ->order_by('id', 'desc')
        ->set_alias([
            'path' => phrase('Path'),
            'method' => phrase('Method'),
            'browser' => phrase('Browser'),
            'platform' => phrase('Platform'),
            'ip_address' => phrase('IP Address'),
            'timestamp' => phrase('Access Time')
        ])
        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->render($this->_table);
    }

    public function truncate()
    {
        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), go_to());
        }

        if ($this->model->truncate($this->_table)) {
            return throw_exception(301, phrase('The log activities was successfully cleaned.'), go_to(null, ['per_page' => null]));
        } else {
            return throw_exception(500, phrase('Unable to clear the log activities.'), go_to());
        }
    }
}
