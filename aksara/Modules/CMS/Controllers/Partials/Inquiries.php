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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Aksara\Laboratory\Core;

class Inquiries extends Core
{
    private string $_table = 'inquiries';

    public function __construct()
    {
        parent::__construct();

        $this->unsetMethod('create');
        $this->restrictOnDemo();
        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        $this->setTitle(phrase('Inquiries'))
        ->setIcon('mdi mdi-message-text')
        ->unsetColumn('id')
        ->unsetField('id, sender_full_name, sender_phone, sender_email, subject, messages')
        ->unsetView('id')

        ->setButton('update', 'update', 'Mark as Followed Up', 'btn-success --modal', 'mdi mdi-check-bold', ['id' => 'id'])

        ->setField([
            'messages' => 'textarea'
        ])
        ->setField('status', 'radio', [
            0 => '<span class="badge bg-warning">' . phrase('Awaiting Response') . '</span>',
            1 => '<span class="badge bg-success">' . phrase('Followed Up') . '</span>'
        ])

        ->setAlias([
            'sender_email' => phrase('Email'),
            'sender_full_name' => phrase('Sender'),
        ])

        ->orderBy('created_at', 'DESC')

        ->render($this->_table);
    }
}
