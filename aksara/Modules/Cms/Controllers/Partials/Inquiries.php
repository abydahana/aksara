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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Aksara\Laboratory\Core;

class Inquiries extends Core
{
    private $_table = 'inquiries';

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
        $this->setTitle(phrase('Inquiries'))
        ->setIcon('mdi mdi-message-text')
        ->unsetColumn('id')
        ->unsetField('id')
        ->unsetView('id')

        ->setAlias([
            'sender_email' => phrase('Email'),
            'sender_full_name' => phrase('Sender')
        ])

        ->orderBy('timestamp', 'DESC')

        ->render($this->_table);
    }
}
