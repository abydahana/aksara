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

        ->add_toolbar('setting', phrase('Setting'), 'btn-dark --modal', 'mdi mdi-cogs')
        ->unset_column('id')
        ->unset_view('id')
        ->render($this->_table);
    }

    public function setting()
    {
        $this->set_method('update')
        ->insert_on_update_fail()

        ->set_title(phrase('Notification Settings'))
        ->set_icon('mdi mdi-cogs')

        ->unset_field('site_id')

        ->set_field([
            'smtp_port' => 'integer',
            'smtp_password' => 'encryption'
        ])

        ->set_validation([
            'whatsapp_api_url' => 'valid_url',
            'smtp_hostname' => 'valid_url',
            'smtp_port' => 'integer'
        ])

        ->set_alias([
            'whatsapp_api_url' => phrase('WhatsApp API URL'),
            'whatsapp_api_key' => phrase('WhatsApp API Key'),
            'smtp_hostname' => phrase('SMTP Hostname'),
            'smtp_port' => phrase('SMTP Port'),
            'smtp_username' => phrase('SMTP Username'),
            'smtp_password' => phrase('SMTP Password')
        ])

        ->merge_field('smtp_hostname, smtp_port')
        ->merge_field('smtp_username, smtp_password')
        ->field_size([
            'smtp_hostname' => 'col-sm-8',
            'smtp_port' => 'col-sm-4'
        ])

        ->set_heading('smtp_hostname', phrase('SMTP Configuration'))

        ->where('site_id', get_setting('id'))
        ->set_default('site_id', get_setting('id'))

        ->render('notifications__settings');
    }

    public function send($id = 0)
    {
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
            // Send email
            $this->_send_email($val);

            // Send WhatsApp
            $this->_send_whatsapp($val);
        }
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
        $host = get_setting('smtp_host');
        $port = get_setting('smtp_port');
        $username = get_setting('smtp_username');
        $password = service('encrypter')->decrypt(base64_decode(get_setting('smtp_password') ?? ''));
        $sender_email = get_setting('smtp_email_masking') ?? service('request')->getServer('SERVER_ADMIN') ?? 'webmaster@' . service('request')->getServer('SERVER_NAME');
        $sender_name = get_setting('smtp_sender_masking') ?? get_setting('app_name');

        if ($host && $username && $password) {
            $config['userAgent'] = 'Aksara';
            $config['protocol'] = 'smtp';
            $config['SMTPHost'] = $host;
            $config['SMTPPort'] = $port;
            $config['SMTPUser'] = $username;
            $config['SMTPPass'] = $password;
        }

        $email->setFrom($sender_email, $sender_name);
        $email->setTo($data->email);
        $email->setSubject($data->title);
        $email->setMessage($data->message);

        try {
            // Send email
            $email->send();
        } catch(\Throwable $e) {
            // return throw_exception(400, array('message' => $email->printDebugger()));
        }
    }

    private function _send_whatsapp($data = [])
    {
        if (! isset($data->phone) || ! filter_var($data->phone, FILTER_SANITIZE_NUMBER_INT)) {
            // Not a valid phone
            return false;
        }

        // Load cURL library
        $client = \Config\Services::curlrequest();

        $response = $client->request('POST', 'http://116.203.191.58/api/send_message', [
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'form_params' => [
                'key' => $key,
                'phone_no' => $data->phone,
                'message' => $data->message,
                'deliveryFlag' => true
            ]
        ]);
    }
}
