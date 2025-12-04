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

namespace Aksara\Libraries;

class Messaging
{
    private $_recipient_email;
    private $_recipient_phone;
    private $_subject;
    private $_message;

    public function __construct()
    {
        // Constructor
    }

    /**
     * Set recipient email
     * @param   null|mixed $email
     */
    public function set_email($email = null)
    {
        $this->_recipient_email = $email;

        return $this;
    }

    /**
     * Set recipient phone
     * @param   null|mixed $phone
     */
    public function set_phone($phone = null)
    {
        $this->_recipient_phone = $phone;

        return $this;
    }

    /**
     * Set subject
     * @param   null|mixed $subject
     */
    public function set_subject($subject = null)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * Set message
     * @param   null|mixed $message
     */
    public function set_message($message = null)
    {
        $this->_message = $message;

        return $this;
    }

    /**
     * Send notification
     */
    public function send(bool $instant = false)
    {
        if (! $this->_recipient_email && ! $this->_recipient_phone) {
            return false;
        }

        if ($instant) {
            // Send email immediately
            $host = get_setting('smtp_host');
            $username = get_setting('smtp_username');
            $password = (get_setting('smtp_password') ? service('encrypter')->decrypt(base64_decode(get_setting('smtp_password'))) : '');
            $sender_email = (get_setting('smtp_email_masking') ? get_setting('smtp_email_masking') : service('request')->getServer('SERVER_ADMIN'));
            $sender_name = (get_setting('smtp_sender_masking') ? get_setting('smtp_sender_masking') : get_setting('app_name'));

            $email = \Config\Services::email();

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

            $email->initialize($config);

            $email->setFrom($sender_email, $sender_name);
            $email->setTo($this->_recipient_email);

            $email->setSubject($this->_subject);
            $email->setMessage('
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta name="viewport" content="width=device-width" />
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <title>
                            ' . $this->_subject . '
                        </title>
                    </head>
                    <body>
                        ' . $this->_message . '
                    </body>
                </html>
            ');

            try {
                // Send email
                $email->send();
            } catch (\Throwable $e) {
                // return throw_exception(400, array('message' => $email->printDebugger()));
            }
        } else {
            // Load model
            $model = new \Aksara\Laboratory\Model();

            // Insert record into notification
            $query = $model->insert(
                'notifier',
                [
                    'phone' => $this->_recipient_phone ?? '',
                    'email' => $this->_recipient_email ?? '',
                    'title' => $this->_subject ?? '',
                    'message' => $this->_message ?? '',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'status' => 0
                ]
            );
        }
    }
}
