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

namespace Aksara\Libraries;

use Throwable;
use Config\Services;

class Messaging
{
    private ?string $_recipientEmail;
    private ?string $_recipientPhone;
    private ?string $_subject;
    private ?string $_message;

    public function __construct()
    {
        // Constructor
    }

    /**
     * Set recipient email
     * @param   null|mixed $email
     */
    public function setEmail($email = null)
    {
        $this->_recipientEmail = $email;

        return $this;
    }

    /**
     * Set recipient phone
     * @param   null|mixed $phone
     */
    public function setPhone($phone = null)
    {
        $this->_recipientPhone = $phone;

        return $this;
    }

    /**
     * Set subject
     * @param   null|mixed $subject
     */
    public function setSubject($subject = null)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * Set message
     * @param   null|mixed $message
     */
    public function setMessage($message = null)
    {
        $this->_message = $message;

        return $this;
    }

    /**
     * Send notification
     */
    public function send(bool $instant = false)
    {
        if (! $this->_recipientEmail && ! $this->_recipientPhone) {
            return false;
        }

        if ($instant) {
            $encrypter = Services::encrypter();
            $request = Services::request();

            // SMTP configuration
            $host = trim((string) get_setting('smtp_host'));
            $username = trim((string) get_setting('smtp_username'));

            $encryptedPassword = get_setting('smtp_password');

            $password = $encryptedPassword
                ? $encrypter->decrypt(base64_decode($encryptedPassword))
                : '';

            /*
            * Do not attempt to send email if SMTP is not configured.
            * This prevents CodeIgniter from falling back to PHP mail()
            * and generating unnecessary log entries.
            */
            if (! $host || ! $username || ! $password) {
                return false;
            }

            $senderEmail = get_setting('smtp_email_masking')
                ?: $request->getServer('SERVER_ADMIN');

            $senderName = get_setting('smtp_sender_masking')
                ?: get_setting('app_name');

            if (! $senderEmail) {
                return false;
            }

            $config = [
                'userAgent' => 'Aksara',
                'protocol' => 'smtp',
                'SMTPCrypto' => 'ssl',
                'SMTPTimeout' => 5,
                'SMTPHost' => (strpos($host, '://') !== false
                    ? trim(substr($host, strpos($host, '://') + 3))
                    : $host),
                'SMTPPort' => (int) get_setting('smtp_port'),
                'SMTPUser' => $username,
                'SMTPPass' => $password,
                'charset' => 'utf-8',
                'newline' => "\r\n",
                'mailType' => 'html',
                'wordWrap' => true,
                'validation' => true,
            ];

            $email = Services::email();
            $email->initialize($config);

            $email->setFrom($senderEmail, $senderName);
            $email->setTo($this->_recipientEmail);

            $email->setSubject($this->_subject);
            $email->setMessage('
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta name="viewport" content="width=device-width" />
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <title>' . $this->_subject . '</title>
                    </head>
                    <body>
                        ' . $this->_message . '
                    </body>
                </html>
            ');

            try {
                return $email->send(false);
            } catch (Throwable $e) {
                return false;
            }
        }

        // Queue notification
        $model = new \Aksara\Laboratory\Model();

        return $model->insert(
            'notifier',
            [
                'phone' => $this->_recipientPhone ?? '',
                'email' => $this->_recipientEmail ?? '',
                'title' => $this->_subject ?? '',
                'message' => $this->_message ?? '',
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 0,
            ]
        );
    }
}
