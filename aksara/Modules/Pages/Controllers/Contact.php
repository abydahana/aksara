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

class Contact extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->valid_token(service('request')->getPost('_token'))) {
            return $this->_send_message();
        }

        $this->set_title(phrase('Contact Us'))
        ->set_icon('mdi mdi-phone-classic')
        ->set_description(phrase('Submit your inquiries or questions to us'))

        ->render();
    }

    public function _send_message()
    {
        $this->form_validation->setRule('full_name', phrase('Full Name'), 'required');
        $this->form_validation->setRule('email', phrase('Email'), 'required|valid_email');
        $this->form_validation->setRule('subject', phrase('Subject'), 'required');
        $this->form_validation->setRule('messages', phrase('Messages'), 'required');
        $this->form_validation->setRule('copy', phrase('Send copy'), 'boolean');

        if ($this->form_validation->run(service('request')->getPost()) === false) {
            return throw_exception(400, $this->form_validation->getErrors());
        }

        $this->model->insert(
            'inquiries',
            [
                'sender_email' => service('request')->getPost('email'),
                'sender_full_name' => htmlspecialchars(service('request')->getPost('full_name')),
                'subject' => htmlspecialchars(service('request')->getPost('subject')),
                'messages' => htmlspecialchars(service('request')->getPost('messages')),
                'timestamp' => date('Y-m-d H:i:s')
            ]
        );

        if (service('request')->getPost('copy')) {
            /**
             * To working with Google SMTP, make sure to activate less secure apps setting
             */
            $this->email = \Config\Services::email();

            $host = get_setting('smtp_host');
            $username = get_setting('smtp_username');
            $password = (get_setting('smtp_password') ? service('encrypter')->decrypt(base64_decode(get_setting('smtp_password'))) : '');
            $sender_email = (get_setting('smtp_email_masking') ? get_setting('smtp_email_masking') : (service('request')->getServer('SERVER_ADMIN') ? service('request')->getServer('SERVER_ADMIN') : 'webmaster@' . service('request')->getServer('SERVER_NAME')));
            $sender_name = (get_setting('smtp_sender_masking') ? get_setting('smtp_sender_masking') : get_setting('app_name'));

            $this->email = \Config\Services::email();

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

            $this->email->initialize($config);

            $this->email->setFrom($sender_email, $sender_name);
            $this->email->setTo(service('request')->getPost('email'));

            $this->email->setSubject(service('request')->getPost('subject'));
            $this->email->setMessage(service('request')->getPost('messages'));

            if (! $this->email->send()) {
                //return throw_exception(400, array('message' => $this->email->printDebugger('header')));
            }
        }

        return throw_exception(301, phrase('Your inquiry was successfully submitted.'), current_page());
    }
}
