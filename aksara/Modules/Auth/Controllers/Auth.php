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

namespace Aksara\Modules\Auth\Controllers;

use Throwable;
use Config\Services;
use Hybridauth\Hybridauth;
use Aksara\Libraries\Messaging;
use Aksara\Laboratory\Core;

class Auth extends Core
{
    public function __construct()
    {
        parent::__construct();

        if ($this->request->getGet('privilege_check')) {
            // Prevent endless redirect
            return throw_exception(403, phrase('You were signed in but have no privilege to access the requested page.'), base_url(null, ['privilege_check' => null, 'redirect' => null]), true);
        }
    }

    public function index()
    {
        // Check if use is already signed in
        if (get_userdata('is_logged')) {
            // Check if request is made through API or not
            if ($this->apiClient) {
                // Requested through API, provide the access token
                return make_json([
                    'status' => 200,
                    'message' => phrase('You are already logged in.'),
                    'access_token' => get_userdata('access_token')
                ]);
            } else {
                // Requested through browser
                return throw_exception(301, phrase('You were signed in'), base_url(($this->request->getGet('redirect') ? $this->request->getGet('redirect') : 'dashboard'), ['privilege_check' => 1, 'redirect' => null]), true);
            }
        } elseif ($this->validToken($this->request->getPost('_token')) || ($this->apiClient && $this->request->getServer('REQUEST_METHOD') == 'POST')) {
            // Apply login attempts limit (prevent bruteforce)
            if (get_userdata('_login_attempt') >= get_setting('login_attempt') && get_userdata('_login_attempt_time') >= time()) {
                // Blacklist the client IP
                $this->model->upsert(
                    'app_users_blocked',
                    [
                        'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
                        'blocked_until' => date('Y-m-d H:i:s', get_userdata('_login_attempt_time')),
                        'blocked_reason' => 'login_attempt'
                    ]
                );

                return throw_exception(400, ['username' => phrase('You are temporarily blocked due do frequent failed login attempts.')]);
            }

            $this->formValidation->setRule('username', phrase('Username'), 'required');
            $this->formValidation->setRule('password', phrase('Password'), 'required');

            if ($this->request->getPost('year')) {
                $this->formValidation->setRule('year', phrase('Year'), 'valid_year');
            }

            // Run form validation
            if ($this->formValidation->run($this->request->getPost()) === false) {
                // Throw validation message
                return throw_exception(400, $this->formValidation->getErrors());
            } else {
                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');

                $execute = $this->model->select('
                    user_id,
                    username,
                    password,
                    group_id,
                    language_id,
                    status
                ')
                ->where('username', $username)
                ->orWhere('email', $username)
                ->get(
                    'app_users',
                    1
                )
                ->row();

                // Check if user is inactive
                if ($execute && 1 != $execute->status) {
                    return throw_exception(400, ['username' => phrase('Your account is temporary disabled or not yet activated.')]);
                } elseif ($execute && password_verify($password . ENCRYPTION_KEY, $execute->password)) {
                    // Check if login attempts failed from the previous session
                    $blocking_check = $this->model->getWhere(
                        'app_users_blocked',
                        [
                            'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress())
                        ],
                        1
                    )
                    ->row();

                    if ($blocking_check) {
                        // Check if blocking time is still available
                        if (strtotime($blocking_check->blocked_until) >= time()) {
                            // Throw the blocking messages
                            return throw_exception(400, ['username' => phrase('You are temporarily blocked due do frequent failed login attempts.')]);
                        } else {
                            // Remove the record from blocking table
                            $this->model->delete(
                                'app_users_blocked',
                                [
                                    'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress())
                                ]
                            );
                        }
                    }

                    // Check if system apply one device login
                    if (get_setting('one_device_login')) {
                        // Get older sessions
                        $sessions = $this->model->select('
                            session_id,
                            timestamp
                        ')
                        ->groupBy('session_id')
                        ->getWhere(
                            'app_log_activities',
                            [
                                'user_id' => $execute->user_id
                            ]
                        )
                        ->result();

                        if ($sessions) {
                            // Older sessions exist
                            foreach ($sessions as $key => $val) {
                                if ($val->session_id && file_exists(WRITEPATH . 'session/' . $val->session_id)) {
                                    // Older session found
                                    try {
                                        // Unlink older session
                                        if (unlink(WRITEPATH . 'session/' . $val->session_id)) {
                                            // Update table to skip getting session_id on next execution
                                            $this->model->update('app_log_activities', ['session_id' => ''], ['session_id' => $val->session_id]);
                                        }
                                    } catch (Throwable $e) {
                                        // Safe abstraction
                                    }
                                }
                            }
                        }
                    }

                    // Regenerate session ID to prevent Session Fixation
                    service('session')->regenerate();

                    // Set the user credential into session
                    set_userdata([
                        'is_logged' => true,
                        'user_id' => $execute->user_id,
                        'username' => $execute->username,
                        'group_id' => $execute->group_id,
                        'language_id' => $execute->language_id,
                        'year' => ($this->_getActiveYears() ? ($this->request->getPost('year') ? $this->request->getPost('year') : date('Y')) : null),
                        'session_generated' => time()
                    ]);

                    // Update the last login timestamp
                    $this->model->update(
                        'app_users',
                        [
                            'last_login' => date('Y-m-d H:i:s')
                        ],
                        [
                            'user_id' => $execute->user_id
                        ],
                        1
                    );

                    // Check if request is made through API or not
                    if ($this->apiClient) {
                        // Set access token
                        set_userdata('access_token', session_id());

                        $this->model->insert(
                            'app_sessions',
                            [
                                'id' => get_userdata('access_token'),
                                'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
                                'timestamp' => date('Y-m-d H:i:s'),
                                'data' => session_encode()
                            ]
                        );

                        // Requested through API, provide the access token
                        return make_json([
                            'status' => 200,
                            'message' => phrase('You were logged in successfully.'),
                            'access_token' => get_userdata('access_token')
                        ]);
                    } else {
                        // Send notification
                        $this->_sendNotification($execute->user_id);

                        $referrer = $this->request->getUserAgent()->getReferrer();
                        $redirect = $this->request->getGet('redirect');

                        if (! $redirect && stripos($referrer, base_url()) !== false) {
                            $redirect = str_replace([base_url(), 'index.php'], '', $referrer);
                        }

                        // Requested through browser
                        return throw_exception(301, phrase('Welcome back') . ', <b>' . get_userdata('first_name') . '</b>! ' . phrase('You have been signed in successfully.'), base_url($redirect), true);
                    }
                }

                // Set the login attempts blocking
                set_userdata([
                    '_login_attempt' => (get_userdata('_login_attempt') ? get_userdata('_login_attempt') : 0) + 1,
                    '_login_attempt_time' => strtotime('+' . get_setting('blocking_time') . ' minute')
                ]);

                // Throw the validation messages
                return throw_exception(400, ['password' => phrase('Username or email and password combination does not match.')]);
            }
        }

        $this->setTitle(phrase('Dashboard Access'))
        ->setIcon('mdi mdi-lock-open-outline')
        ->setDescription(phrase('Please enter your account information to signing in.'))

        ->setOutput([
            'years' => $this->_getActiveYears(),
            'activation' => $this->_getActivation()
        ])

        ->modalSize((get_setting('frontend_registration') ? 'modal-lg' : 'modal-md'))

        ->render();
    }

    /**
     * Sign out
     */
    public function signOut()
    {
        /**
         * Prepare to revoke provider token
         */
        if (get_userdata('oauth_uid')) {
            // Retrieve service provider from sso uid
            $provider = $this->model->getWhere(
                'app_users_oauth',
                [
                    'access_token' => get_userdata('oauth_uid')
                ],
                1
            )
            ->row('service_provider');

            $config = [
                // Location where to redirect users once they authenticate with a provider
                'callback' => base_url('auth/sso/' . $provider),

                // Providers specifics
                'providers' => [
                    'google' => [
                        'enabled' => ('google' == $provider ? true : false),
                        'keys' => [
                            'id' => get_setting('google_client_id'),
                            'secret' => get_setting('google_client_secret')
                        ]
                    ],
                    'facebook' => [
                        'enabled' => ('facebook' == $provider ? true : false),
                        'keys' => [
                            'id' => get_setting('facebook_app_id'),
                            'secret' => get_setting('facebook_app_secret')
                        ]
                    ]
                ],

                'approval_prompt' => 'force'
            ];

            try {
                // Instantiate adapter directly
                $hybridauth = new Hybridauth($config);

                // Instantiate adapter directly
                $adapter = $hybridauth->authenticate($provider);

                // Disconnect the adapter (log out)
                $adapter->disconnect();
            } catch (Throwable $e) {
                // Safe abstraction
            }
        }

        // Remove session from database
        $this->model->delete(
            'app_sessions',
            [
                'id' => $this->request->getHeaderLine('X-ACCESS-TOKEN') ?? session_id()
            ]
        );

        // Backup session items
        $_login_attempt = get_userdata('_login_attempt');
        $_login_attempt_time = get_userdata('_login_attempt_time');
        $_spam_timer = get_userdata('_spam_timer');

        // Destroy session
        $session = Services::session();
        $session->destroy();

        // Rollback login attempt config
        set_userdata([
            '_login_attempt' => $_login_attempt,
            '_login_attempt_time' => $_login_attempt_time,
            '_spam_timer' => $_spam_timer
        ]);

        return throw_exception(301, phrase('You were signed out'), base_url(), true);
    }

    /**
     * Get active years
     */
    private function _getActiveYears()
    {
        $output = [];

        $query = $this->model->getWhere(
            'app_years',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $output[] = [
                    'value' => $val->year,
                    'label' => $val->year,
                    'selected' => $val->default
                ];
            }
        }

        return $output;
    }

    /**
     * Check activation
     */
    private function _getActivation()
    {
        if (! $this->request->getGet('activation')) {
            return false;
        }

        $user_id = 0;

        try {
            $encrypter = Services::encrypter();

            $user_id = $encrypter->decrypt(base64_decode($this->request->getGet('activation')));
        } catch (Throwable $e) {
            // Safe abstraction
        }

        if ($this->model->getWhere('app_users_hashes', ['user_id' => $user_id], 1)->row()) {
            return true;
        }

        return false;
    }

    /**
     * Send notification
     */
    private function _sendNotification($user_id = 0)
    {
        $query = $this->model->getWhere(
            'app_users',
            [
                'user_id' => $user_id
            ],
            1
        )
        ->row();

        if ($query) {
            $messaging = new Messaging();

            $messaging->setEmail($query->email)
            ->setPhone($query->phone)
            ->setSubject(phrase('Login Activity'))
            ->setMessage(
                phrase('Hello') . ', ' . get_userdata('first_name') . '.' .
                "\n" .
                phrase('There is a login activity recently made from your account.') . ' ' . phrase('You can restore your account if the login action was not carried out by you.') .
                "\n\n" .
                phrase('You can ignore this message if the login was made by yourself.')
            )
            ->send(true);
        }
    }
}
