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

namespace Aksara\Modules\Notifier\Controllers;

use Aksara\Laboratory\Core;

class Notifier extends Core
{
    private $_table = 'notifier';

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        $this->unsetMethod('create, update')
        ->setTitle(phrase('Notifier'))
        ->setIcon('mdi mdi-bullhorn')

        ->addToolbar('send', phrase('Resend'), 'btn-primary --xhr show-progress', 'mdi mdi-send')
        ->addToolbar('setting', phrase('Setting'), 'btn-dark --modal', 'mdi mdi-cogs')

        ->addButton('send', phrase('Resend'), 'btn-dark --xhr show-progress', 'mdi mdi-send', ['id' => 'id'])

        ->unsetColumn('id, message')
        ->unsetView('id')

        ->setField([
            'message' => 'textarea',
            'timestamp' => 'datetime'
        ])
        ->setField('status', 'radio', [
            0 => phrase('Pending'),
            1 => phrase('Partially Sent'),
            2 => phrase('Fully Sent')
        ])

        ->orderBy('timestamp', 'DESC')

        ->render($this->_table);
    }
}
