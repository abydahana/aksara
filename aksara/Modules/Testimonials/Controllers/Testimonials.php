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

namespace Aksara\Modules\Testimonials\Controllers;

use Aksara\Laboratory\Core;

class Testimonials extends Core
{
    private string $_table = 'testimonials';

    /**
     * Rate limit window in seconds (default 86400 = 24 hours / 1 day)
     */
    private int $_rateLimit = 86400;

    public function __construct()
    {
        parent::__construct();

        $this->limit(10);
        $this->allowPublicFormSubmission();
        $this->setUploadPath('testimonials');
    }

    public function index()
    {
        if (! service('request')->getPost('_token')) {
            // Load captcha helper for guest users
            if (! get_userdata('is_logged')) {
                helper('captcha');

                $this->setOutput('captcha', generate_captcha());
            }
        }

        $this->setTitle(phrase('Testimonials'))
        ->setDescription(phrase('What have people said about us?'), (service('request')->getGet('q') ? phrase('No testimonial matches your search.') : phrase('No testimonial is available at the moment.')))
        ->setIcon('mdi mdi-bullhorn-outline')

        ->select('app_users.username')
        ->join(
            'app_users',
            'app_users.user_id = testimonials.created_by',
            'LEFT'
        )

        ->where([
            'status' => 1
        ])

        ->orderBy('testimonials.created_at', 'DESC')

        ->render($this->_table);
    }

    public function create()
    {
        // Rate limit check: 1 device / user 1 testimonial per day
        if ($this->_checkRateLimit()) {
            return throw_exception(400, ['quota_exceeded' => phrase('You have already submitted a testimonial today. Please try again tomorrow.')]);
        }

        $this->allowTokenFrom('testimonials');

        // Add captcha validation for guest users
        if (get_userdata('is_logged')) {
            $this->setDefault([
                'first_name' => get_userdata('first_name'),
                'last_name' => get_userdata('last_name')
            ]);
        } else {
            $this->setValidation([
                'first_name' => 'required|string',
                'last_name' => 'string',
                'captcha' => 'required|regex_match[/' . get_userdata('captcha') . '/i]'
            ]);
        }

        $this->setTitle(phrase('Testimonials'))
        ->setDescription(phrase('What have people said about us?'))
        ->setIcon('mdi mdi-bullhorn-outline')

        ->unsetField('testimonial_id, photo')
        ->setField([
            'testimonial_content' => 'textarea',
            'rating' => 'number'
        ])
        ->setDefault([
            'photo' => 'placeholder.png', // Will be overridden in beforeInsert
            'status' => 0,
            'created_by' => get_userdata('user_id')
        ])
        ->setValidation([
            'testimonial_content' => 'required|string',
            'rating' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]'
        ])
        ->setAlias([
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'photo' => phrase('Photo'),
            'testimonial_content' => phrase('Your Testimonial'),
            'rating' => phrase('Rating'),
            'captcha' => phrase('Captcha')
        ])

        ->render($this->_table);
    }

    /**
     * Before insert hook — copy user photo to testimonials folder
     */
    protected function beforeInsert()
    {
        // Rate limit check on before insert
        if ($this->_checkRateLimit()) {
            return throw_exception(400, ['quota_exceeded' => phrase('You have already submitted a testimonial today. Please try again tomorrow.')]);
        }

        if (get_userdata('is_logged')) {
            $userPhoto = get_userdata('photo');

            if ($userPhoto && 'placeholder.png' !== $userPhoto) {
                $this->_copyUserPhoto($userPhoto);
            }
        }
    }

    /**
     * After insert hook — redirect with success message
     */
    protected function afterInsert()
    {
        // Unset stored captcha
        if (! get_userdata('is_logged')) {
            unset_userdata(['captcha', 'captcha_file']);
        }

        // Set rate limit cache
        if ($this->_rateLimit) {
            $ipAddress = service('request')->getIPAddress();
            $userAgent = service('request')->getUserAgent()->getAgentString() ?? '';
            $deviceHash = md5((get_userdata('user_id') ?: $ipAddress) . '_' . $userAgent);
            $cacheKey = 'rate_limit_testimonial_' . $deviceHash;

            service('cache')->save($cacheKey, true, $this->_rateLimit);
        }

        return throw_exception(301, phrase('Your testimonial was successfully submitted and will be reviewed by our team.'), current_page('../', ['success' => 1]));
    }

    /**
     * Check rate limit: 1 device / user 1 testimonial per $_rateLimit seconds
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
        $cacheKey = 'rate_limit_testimonial_' . $deviceHash;

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
                'path' => 'testimonials',
                'method' => 'create',
                'timestamp >=' => $timeWindow
            ], 1)->row();

            if ($existingActivity) {
                service('cache')->save($cacheKey, true, $this->_rateLimit);

                return true;
            }
        }

        return false;
    }

    /**
     * Copy user photo from uploads/users to uploads/testimonials
     * Including thumbs and icons subdirectories
     */
    private function _copyUserPhoto(string $filename): void
    {
        $sourcePath = UPLOAD_PATH . '/users/';
        $destPath = UPLOAD_PATH . '/testimonials/';

        // Ensure destination directories exist
        $dirs = ['', 'thumbs/', 'icons/'];

        foreach ($dirs as $dir) {
            if (! is_dir($destPath . $dir)) {
                mkdir($destPath . $dir, 0755, true);
            }
        }

        // Copy main photo
        if (is_file($sourcePath . $filename)) {
            copy($sourcePath . $filename, $destPath . $filename);
        }

        // Copy thumb
        if (is_file($sourcePath . 'thumbs/' . $filename)) {
            copy($sourcePath . 'thumbs/' . $filename, $destPath . 'thumbs/' . $filename);
        }

        // Copy icon
        if (is_file($sourcePath . 'icons/' . $filename)) {
            copy($sourcePath . 'icons/' . $filename, $destPath . 'icons/' . $filename);
        }

        // Override the photo field default with the user's photo filename
        $this->setDefault('photo', $filename);
    }
}
