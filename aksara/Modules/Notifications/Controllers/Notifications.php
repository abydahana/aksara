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

namespace Aksara\Modules\Notifications\Controllers;

class Notifications extends \Aksara\Laboratory\Core
{
    private $_table = 'notifications';

    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');
    }

    public function index()
    {
        $this->unset_method('create, update')
        ->set_title(phrase('Notifications'))
        ->set_icon('mdi mdi-bullhorn')
        
        ->add_toolbar('send', phrase('Resend'), 'btn-primary --xhr show-progress', 'mdi mdi-send')
        ->add_toolbar('setting', phrase('Setting'), 'btn-dark --modal', 'mdi mdi-cogs')

        ->add_button('send', phrase('Resend'), 'btn-dark --xhr show-progress', 'mdi mdi-send', ['id' => 'id'])

        ->unset_column('id, message')
        ->unset_view('id')

        ->set_field([
            'message' => 'textarea',
            'timestamp' => 'datetime'
        ])
        ->set_field('status', 'radio', [
            0 => phrase('Pending'),
            1 => phrase('Partially Sent'),
            2 => phrase('Fully Sent')
        ])

        ->order_by('timestamp', 'DESC')

        ->render($this->_table);
    }
}
