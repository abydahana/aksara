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

namespace Aksara\Modules\Cms\Controllers\Partials;

use Aksara\Laboratory\Core;

class Inquiries extends Core
{
    private $_table = 'inquiries';

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
        $this->set_title(phrase('Inquiries'))
        ->set_icon('mdi mdi-message-text')
        ->unset_column('id')
        ->unset_field('id')
        ->unset_view('id')

        ->set_alias([
            'sender_email' => phrase('Email'),
            'sender_full_name' => phrase('Sender')
        ])

        ->order_by('timestamp', 'DESC')

        ->render($this->_table);
    }
}
