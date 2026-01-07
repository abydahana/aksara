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

class Setting extends Core
{
    private $_table = 'notifier__settings';

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');
        $this->parentModule('notifier');

        $this->setMethod('update');
        $this->permitUpsert();
    }

    public function index()
    {
        $this->setTitle(phrase('WhatsApp API Settings'))
        ->setIcon('mdi mdi-whatsapp')

        ->setDescription('Cron job command: <br /><input value="*/1 * * * * curl ' . base_url('notifier/send') . ' > /dev/null 2>&1" class="form-control" disabled>')

        ->unsetField('site_id')

        ->setField([
            'whatsapp_api_header' => 'attribution',
            'whatsapp_api_payload' => 'attribution'
        ])

        ->setValidation([
            'whatsapp_api_url' => 'required|valid_url'
        ])

        ->setAlias([
            'whatsapp_api_url' => phrase('WhatsApp API URL'),
            'whatsapp_api_header' => phrase('WhatsApp API Headers'),
            'whatsapp_api_payload' => phrase('WhatsApp API Payloads')
        ])

        ->where('site_id', get_setting('id'))
        ->setDefault('site_id', get_setting('id'))

        ->render($this->_table);
    }
}
