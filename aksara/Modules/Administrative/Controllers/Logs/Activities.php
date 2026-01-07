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

use Aksara\Laboratory\Core;

class Activities extends Core
{
    private $_table = 'app__log_activities';

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
        ->setPrimary('id')
        ->unsetColumn('id, user_id, browser, session_id, query')
        ->unsetView('id, user_id, session_id, query')
        ->addToolbar('truncate', phrase('Clear Logs'), 'btn-primary --open-delete-confirm', 'mdi mdi-delete-empty')
        ->setField('timestamp', 'current_timestamp')
        ->setField('first_name', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->setField('path', 'hyperlink', '{{ path }}/{{ method }}', '{{ query }}')
        ->columnOrder('first_name')
        ->viewOrder('first_name')
        ->select('app__users.first_name')
        ->join('app__users', 'app__users.user_id = ' . $this->_table . '.user_id')
        ->orderBy('id', 'desc')
        ->setAlias([
            'path' => phrase('Path'),
            'method' => phrase('Method'),
            'browser' => phrase('Browser'),
            'platform' => phrase('Platform'),
            'ip_address' => phrase('IP Address'),
            'timestamp' => phrase('Access Time')
        ])
        ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Full Name'))
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
