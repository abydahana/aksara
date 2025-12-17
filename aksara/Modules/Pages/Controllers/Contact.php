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

namespace Aksara\Modules\Pages\Controllers;

use Config\Services;
use Aksara\Laboratory\Core;

class Contact extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->valid_token($this->request->getPost('_token'))) {
            return $this->_send_message();
        }

        $this->set_title(phrase('Contact Us'))
        ->set_icon('mdi mdi-phone-classic')
        ->set_description(phrase('Submit your inquiries or questions to us.'))

        ->render();
    }

    public function _send_message()
    {
        $this->form_validation->setRule('full_name', phrase('Full Name'), 'required');
        $this->form_validation->setRule('email', phrase('Email'), 'required|valid_email');
        $this->form_validation->setRule('subject', phrase('Subject'), 'required');
        $this->form_validation->setRule('messages', phrase('Messages'), 'required');
        $this->form_validation->setRule('copy', phrase('Send copy'), 'boolean');

        if ($this->form_validation->run($this->request->getPost()) === false) {
            return throw_exception(400, $this->form_validation->getErrors());
        }

        $this->model->insert(
            'inquiries',
            [
                'sender_email' => $this->request->getPost('email'),
                'sender_full_name' => htmlspecialchars($this->request->getPost('full_name')),
                'subject' => htmlspecialchars($this->request->getPost('subject')),
                'messages' => htmlspecialchars($this->request->getPost('messages')),
                'timestamp' => date('Y-m-d H:i:s')
            ]
        );

        if ($this->request->getPost('copy')) {
            /**
             * To working with Google SMTP, make sure to activate less secure apps setting
             */
            $encrypter = Services::encrypter();

            $host = get_setting('smtp_host');
            $username = get_setting('smtp_username');
            $password = (get_setting('smtp_password') ? $encrypter->decrypt(base64_decode(get_setting('smtp_password'))) : '');
            $sender_email = (get_setting('smtp_email_masking') ? get_setting('smtp_email_masking') : ($this->request->getServer('SERVER_ADMIN') ? $this->request->getServer('SERVER_ADMIN') : 'webmaster@' . $this->request->getServer('SERVER_NAME')));
            $sender_name = (get_setting('smtp_sender_masking') ? get_setting('smtp_sender_masking') : get_setting('app_name'));

            if ($host && $username && $password) {
                $config['userAgent'] = 'Aksara';
                $config['protocol'] = 'smtp';
                $config['SMTPCrypto'] = 'ssl';
                $config['SMTPTimeout'] = 5;
                $config['SMTPHost'] = (strpos($host, '://') !== false ? trim(substr($host, strpos($host, '://') + 3)) : $host);
                $config['SMTPPort'] = get_setting('smtp_port');
                $config['SMTPUser'] = $username;
                $config['SMTPPass'] = $password;
            } else {
                $config['protocol'] = 'mail';
            }

            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['mailType'] = 'html'; // Text or html
            $config['wordWrap'] = true;
            $config['validation'] = true; // Bool whether to validate email or not

            $email = Services::email();

            $email->initialize($config);
            $email->setFrom($sender_email, $sender_name);
            $email->setTo($this->request->getPost('email'));
            $email->setSubject($this->request->getPost('subject'));
            $email->setMessage($this->request->getPost('messages'));

            if (! $email->send()) {
                // Get delivery errors
                $error_message = $email->printDebugger();

                // Log errors
                log_message('error', 'Email failed to send: ' . $error_message);

                return throw_exception(400, ['message' => phrase('An unknown error occurred during email delivery.')]);
            }
        }

        return throw_exception(301, phrase('Your inquiry was successfully submitted.'), current_page());
    }
}
