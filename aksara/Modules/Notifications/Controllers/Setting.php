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

class Setting extends \Aksara\Laboratory\Core
{
    private $_table = 'notifications';

    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');
        $this->parent_module('notifications');

        $this->set_method('update');
        $this->permit_upsert();
    }

    public function index()
    {
        $this->set_title(phrase('WhatsApp API Settings'))
        ->set_icon('mdi mdi-whatsapp')

        ->set_description('Cron job command: <br /><input value="*/1 * * * * curl ' . base_url('notifications/send') . ' > /dev/null 2>&1" class="form-control" disabled>')

        ->unset_field('site_id')

        ->set_field([
            'whatsapp_api_header' => 'attribution',
            'whatsapp_api_payload' => 'attribution'
        ])

        ->set_validation([
            'whatsapp_api_url' => 'required|valid_url'
        ])

        ->set_alias([
            'whatsapp_api_url' => phrase('WhatsApp API URL'),
            'whatsapp_api_header' => phrase('WhatsApp API Headers'),
            'whatsapp_api_payload' => phrase('WhatsApp API Payloads')
        ])

        ->where('site_id', get_setting('id'))
        ->set_default('site_id', get_setting('id'))

        ->render('notifications__settings');
    }
}
