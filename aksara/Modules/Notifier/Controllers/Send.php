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

class Send extends \Aksara\Laboratory\Core
{
    private $_table = 'notifier';

    public function __construct()
    {
        parent::__construct();

        $this->set_theme('backend');
        $this->parent_module('notifier');
    }

    public function index()
    {
        $id = service('request')->getGet('id');

        // Get unsent email notification
        if ($id) {
            $this->model->where('id', $id);
        }

        $query = $this->model->get_where(
            $this->_table,
            [
                'status' => 0
            ]
        )
        ->result();

        foreach ($query as $key => $val) {
            // Send WhatsApp
            $this->_send_email($val);
        }

        // Get unsent WhatsApp notification
        if ($id) {
            $this->model->where('id', $id);
        }

        $query = $this->model->get_where(
            $this->_table,
            [
                'status != ' => 2
            ]
        )
        ->result();

        foreach ($query as $key => $val) {
            // Send WhatsApp
            $this->_send_whatsapp($val);
        }

        return throw_exception(301, phrase('The message was sent successfully.'), go_to('../', ['id' => null]));
    }

    private function _send_email($data = [])
    {
        if (! isset($data->email) || ! filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            // Not a valid email
            return false;
        }

        // Load email library
        $email = \Config\Services::email();

        // Default config
        $config = [
            'charset' => 'UTF-8',
            'newline' => "\r\n",
            'wordWrap' => true,
            'validation' => true,
            'mailType' => 'html'
        ];

        // To working with Google SMTP, make sure to activate less secure apps setting
        $host = get_setting('smtp_hostname');
        $port = get_setting('smtp_port');
        $username = get_setting('smtp_username');
        $password = (get_setting('smtp_password') ? service('encrypter')->decrypt(base64_decode(get_setting('smtp_password') ?? '')) : null);
        $sender_email = $username ?? service('request')->getServer('SERVER_ADMIN') ?? 'webmaster@' . service('request')->getServer('SERVER_NAME');

        if ($host && $username && $password) {
            $config['userAgent'] = 'Aksara';
            $config['protocol'] = 'smtp';
            $config['SMTPHost'] = $host;
            $config['SMTPPort'] = $port;
            $config['SMTPUser'] = $username;
            $config['SMTPPass'] = $password;
        }

        $email->setFrom($sender_email, get_setting('app_name'));
        $email->setTo($data->email);
        $email->setSubject($data->title);
        $email->setMessage($data->message);

        try {
            // Send email
            if ($email->send()) {
                // Update delivery status
                $this->model->update(
                    $this->_table,
                    [
                        'status' => 1
                    ],
                    [
                        'id' => $data->id
                    ]
                );
            }
        } catch (\Throwable $e) {
            if ('127.0.0.1' !== service('request')->getServer('REMOTE_ADDR')) {
                // Return for non crontab only
                return throw_exception(500, $email->printDebugger());
            }
        }
    }

    private function _send_whatsapp($data = [])
    {
        if (! isset($data->phone) || ! filter_var($data->phone, FILTER_SANITIZE_NUMBER_INT)) {
            // Not a valid phone
            return false;
        }

        // Get notification config
        $notifier_config = $this->model->get_where(
            'notifier__settings',
            [
                'site_id' => get_setting('id')
            ],
            1
        )
        ->row();

        if (! $notifier_config || ! $notifier_config->whatsapp_api_url) {
            return false;
        }

        try {
            $headers = [];
            $payloads = [];
            $api_headers = json_decode($notifier_config->whatsapp_api_header ?? '[]', true) ?? [];
            $api_payloads = json_decode($notifier_config->whatsapp_api_payload ?? '[]', true) ?? [];

            foreach ($api_headers as $key => $val) {
                if (! $key || ! $val) {
                    continue;
                }

                // Re-assign headers
                $headers[$key] = $val;
            }

            foreach ($api_payloads as $key => $val) {
                if (! $key || ! $val) {
                    continue;
                }

                // Replace recipient parameter
                $val = preg_replace("/\{\{(\s+)?(recipient)(\s+)?\}\}/", '+62' . (int) str_replace(' ', '', $data->phone), $val);

                // Replace message parameter
                $val = preg_replace("/\{\{(\s+)?(message)(\s+)?\}\}/", str_replace(["\r\n"], "\n", $data->message), $val);

                // Re-assign payloads
                $payloads[$key] = $val;
            }

            // Load cURL library
            $client = \Config\Services::curlrequest();

            $request = $client->request('POST', $notifier_config->whatsapp_api_url, [
                'verify' => false,
                'headers' => $headers,
                'json' => $payloads
            ]);

            $response = $request->getBody();

            if (is_json($response)) {
                $response = json_decode($response);
            }

            if ('success' == $response || (isset($response->message_status) && strtolower($response->message_status) == 'success') || (isset($response->code) && 200 == $response->code)) {
                // Update delivery status
                $this->model->update(
                    $this->_table,
                    [
                        'status' => 2
                    ],
                    [
                        'id' => $data->id
                    ]
                );
            }
        } catch (\Throwable $e) {
            if ('127.0.0.1' !== service('request')->getServer('REMOTE_ADDR')) {
                // Return for non crontab only
                return throw_exception(500, $e->getMessage());
            }
        }
    }
}
