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

namespace Aksara\Modules\Pages\Controllers;

use Throwable;
use Config\Services;
use Aksara\Laboratory\Core;
use Aksara\Libraries\Messaging;

class Contact extends Core
{
    private $_table = 'inquiries';

    public function __construct()
    {
        parent::__construct();

        $this->setMethod('create');
        $this->allowPublicFormSubmission();
    }

    public function index()
    {
        if (! service('request')->getPost('_token')) {
            // Load captcha helper
            helper('captcha');

            $this->setOutput('captcha', generate_captcha());
        }

        $this->setTitle(phrase('Contact Us'))
        ->setIcon('mdi mdi-phone-classic')
        ->setDescription(phrase('Submit your inquiries or questions to us.'))

        ->addField('copy', 'boolean')

        ->setField([
            'email' => 'email',
            'messages' => 'textarea',
            'copy' => 'boolean'
        ])

        ->setValidation([
            'sender_full_name' => 'required',
            'sender_phone' => 'required',
            'sender_email' => 'required|valid_email',
            'subject' => 'required',
            'messages' => 'required',
            'captcha' => 'required|regex_match[/' . get_userdata('captcha') . '/i]',
            'copy' => 'boolean'
        ])
        ->setAlias([
            'sender_full_name' => phrase('Full Name'),
            'sender_phone' => phrase('Phone'),
            'sender_email' => phrase('Email'),
            'subject' => phrase('Subject'),
            'messages' => phrase('Messages'),
            'captcha' => phrase('Captcha'),
            'copy' => phrase('Copy Message')
        ])
        ->render($this->_table);
    }

    public function afterInsert()
    {
        if ($this->request->getPost('copy')) {
            $messaging = new Messaging();

            $messaging->setPhone($this->request->getPost('sender_phone'))
            ->setEmail($this->request->getPost('sender_email'))
            ->setSubject($this->request->getPost('subject'))
            ->setMessage($this->request->getPost('messages'))
            ->send(true);
        }

        // Unset stored captcha
        unset_userdata(['captcha', 'captcha_file']);

        return throw_exception(301, phrase('Your inquiry was successfully submitted.'), current_page(null, ['success' => 1]));
    }
}
