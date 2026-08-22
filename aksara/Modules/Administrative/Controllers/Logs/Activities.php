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

namespace Aksara\Modules\Administrative\Controllers\Logs;

ini_set('memory_limit', -1);

use Aksara\Laboratory\Core;

class Activities extends Core
{
    private string $_table = 'app_log_activities';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('create, update, clone');
    }

    public function index()
    {
        $this->setTitle(phrase('Log Activities'))
        ->setIcon('mdi mdi-information-outline')

        ->addToolbar('truncate', phrase('Clear Logs'), 'btn-primary --open-delete-confirm', 'mdi mdi-delete-empty')

        ->unsetColumn('id, browser, session_id, query')
        ->unsetView('id, session_id, query')

        ->setRelation(
            'user_id',
            'app_users.user_id',
            '{{ first_name }} {{ last_name }}'
        )

        ->setField('timestamp', 'current_timestamp')
        ->setField('path', 'hyperlink', '{{ path }}/{{ method }}', '{{ query }}')
        ->setField('user_id', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->setField('ip_address', 'hyperlink', 'https://ipinfo.io/{{ip_address}}', null, true)

        ->orderBy('id', 'desc')

        ->setAlias([
            'user_id' => phrase('User'),
            'path' => phrase('Path'),
            'method' => phrase('Method'),
            'browser' => phrase('Browser'),
            'platform' => phrase('Platform'),
            'ip_address' => phrase('IP Address'),
            'timestamp' => phrase('Access Time')
        ])
        ->render($this->_table);
    }

    public function truncate()
    {
        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), go_to());
        }

        if ($this->model->truncate($this->_table)) {
            return throw_exception(301, phrase('The log activities was successfully cleaned.'), go_to(null, ['page' => null]));
        } else {
            return throw_exception(500, phrase('Unable to clear the log activities.'), go_to());
        }
    }
}
