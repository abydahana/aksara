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
    private string $_table = 'inquiries';

    /**
     * Rate limit window in seconds (default 3600 = 1 hour)
     */
    private int $_rateLimit = 3600;

    public function __construct()
    {
        parent::__construct();

        $this->setMethod('create');
        $this->allowPublicFormSubmission();
    }

    public function index()
    {
        if (service('request')->is('post') && $this->_checkRateLimit()) {
            return throw_exception(400, ['quota_exceeded' => phrase('You have reached the submission rate limit. Please try again later.')]);
        }

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

    public function beforeInsert()
    {
        if ($this->_checkRateLimit()) {
            return throw_exception(400, ['quota_exceeded' => phrase('You have reached the submission rate limit. Please try again later.')]);
        }
    }

    public function afterInsert()
    {
        // Set rate limit cache
        if ($this->_rateLimit) {
            $ipAddress = service('request')->getIPAddress();
            $userAgent = service('request')->getUserAgent()->getAgentString() ?? '';
            $deviceHash = md5((get_userdata('user_id') ?: $ipAddress) . '_' . $userAgent);
            $cacheKey = 'rate_limit_contact_' . $deviceHash;

            service('cache')->save($cacheKey, true, $this->_rateLimit);
        }

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

    /**
     * Check rate limit: 1 device / user 1 submission per $_rateLimit seconds
     * Handles cache clearing gracefully using DB ground truth for both users and guests.
     */
    private function _checkRateLimit(): bool
    {
        if (! $this->_rateLimit) {
            return false;
        }

        $ipAddress = service('request')->getIPAddress();
        $userAgent = service('request')->getUserAgent()->getAgentString() ?? '';
        $deviceHash = md5((get_userdata('user_id') ?: $ipAddress) . '_' . $userAgent);
        $cacheKey = 'rate_limit_contact_' . $deviceHash;

        // 1. Fast path: check cache
        if (service('cache')->get($cacheKey)) {
            return true;
        }

        // 2. Fallback if cache was cleared: check DB ground truth within rate limit window
        $timeWindow = date('Y-m-d H:i:s', time() - $this->_rateLimit);

        if (get_userdata('is_logged')) {
            $existing = $this->model->getWhere($this->_table, [
                'created_by' => get_userdata('user_id'),
                'created_at >=' => $timeWindow
            ], 1)->row();

            if ($existing) {
                service('cache')->save($cacheKey, true, $this->_rateLimit);

                return true;
            }
        } else {
            // Check activity log for guest IP submission within rate limit window
            $existingActivity = $this->model->getWhere('app_log_activities', [
                'ip_address' => $ipAddress,
                'path' => 'pages/contact',
                'timestamp >=' => $timeWindow
            ], 1)->row();

            if ($existingActivity) {
                service('cache')->save($cacheKey, true, $this->_rateLimit);

                return true;
            }
        }

        return false;
    }
}
