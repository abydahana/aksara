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

namespace Aksara\Modules\Auth\Controllers;

use Hybridauth\Hybridauth;

class Auth extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        if (service('request')->getGet('privilege_check')) {
            // Prevent endless redirect
            return throw_exception(403, phrase('You were signed in but have no privilege to access the requested page.'), base_url(null, ['privilege_check' => null, 'redirect' => null]), true);
        }
    }

    public function index()
    {
        // Check if use is already signed in
        if (get_userdata('is_logged')) {
            // Check if request is made through API or not
            if ($this->api_client) {
                // Requested through API, provide the access token
                return make_json([
                    'status' => 200,
                    'message' => phrase('You were signed in.'),
                    'access_token' => session_id()
                ]);
            } else {
                // Requested through browser
                return throw_exception(301, phrase('You were signed in.'), base_url((service('request')->getGet('redirect') ? service('request')->getGet('redirect') : 'dashboard'), ['privilege_check' => 1, 'redirect' => null]), true);
            }
        } elseif ($this->valid_token(service('request')->getPost('_token')) || ($this->api_client && service('request')->getServer('REQUEST_METHOD') == 'POST')) {
            // Apply login attempts limit (prevent bruteforce)
            if (get_userdata('_login_attempt') >= get_setting('login_attempt') && get_userdata('_login_attempt_time') >= time()) {
                // Check if login attempts failed from the previous session
                $blocking_check = $this->model->get_where(
                    'app__users_blocked',
                    [
                        'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
                    ],
                    1
                )
                ->row();

                if ($blocking_check) {
                    // Update the blocked time of blacklisted client IP
                    $this->model->update(
                        'app__users_blocked',
                        [
                            'blocked_until' => date('Y-m-d H:i:s', get_userdata('_login_attempt_time'))
                        ],
                        [
                            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
                        ]
                    );
                } else {
                    // Blacklist the client IP
                    $this->model->insert(
                        'app__users_blocked',
                        [
                            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
                            'blocked_until' => date('Y-m-d H:i:s', get_userdata('_login_attempt_time'))
                        ]
                    );
                }

                return throw_exception(400, ['username' => phrase('You are temporarily blocked due do frequent failed login attempts.')]);
            }

            $this->form_validation->setRule('username', phrase('Username'), 'required');
            $this->form_validation->setRule('password', phrase('Password'), 'required');

            if (service('request')->getPost('year')) {
                $this->form_validation->setRule('year', phrase('Year'), 'valid_year');
            }

            // Run form validation
            if ($this->form_validation->run(service('request')->getPost()) === false) {
                // Throw validation message
                return throw_exception(400, $this->form_validation->getErrors());
            } else {
                $username = service('request')->getPost('username');
                $password = service('request')->getPost('password');

                $execute = $this->model->select('
                    user_id,
                    username,
                    password,
                    group_id,
                    language_id,
                    status
                ')
                ->where('username', $username)
                ->or_where('email', $username)
                ->get(
                    'app__users',
                    1
                )
                ->row();

                // Check if user is inactive
                if ($execute && 1 != $execute->status) {
                    return throw_exception(404, phrase('Your account is temporary disabled or not yet activated.'));
                } elseif ($execute && password_verify($password . ENCRYPTION_KEY, $execute->password)) {
                    // Check if login attempts failed from the previous session
                    $blocking_check = $this->model->get_where(
                        'app__users_blocked',
                        [
                            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
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
                                'app__users_blocked',
                                [
                                    'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
                                ]
                            );
                        }
                    }

                    // Update the last login timestamp
                    $this->model->update(
                        'app__users',
                        [
                            'last_login' => date('Y-m-d H:i:s')
                        ],
                        [
                            'user_id' => $execute->user_id
                        ],
                        1
                    );

                    // Check if system apply one device login
                    if (get_setting('one_device_login')) {
                        // Get older sessions
                        $sessions = $this->model->select('
                            session_id,
                            timestamp
                        ')
                        ->group_by('session_id')
                        ->get_where(
                            'app__log_activities',
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
                                            $this->model->update('app__log_activities', ['session_id' => ''], ['session_id' => $val->session_id]);
                                        }
                                    } catch (\Throwable $e) {
                                        // Safe abstraction
                                    }
                                }
                            }
                        }
                    }

                    // Set the user credential into session
                    set_userdata([
                        'is_logged' => true,
                        'user_id' => $execute->user_id,
                        'username' => $execute->username,
                        'group_id' => $execute->group_id,
                        'language_id' => $execute->language_id,
                        'year' => ($this->_get_active_years() ? (service('request')->getPost('year') ? service('request')->getPost('year') : date('Y')) : null),
                        'session_generated' => time()
                    ]);

                    // Check if request is made through API or not
                    if ($this->api_client) {
                        $session_id = session_id();

                        $this->model->insert(
                            'app__sessions',
                            [
                                'id' => $session_id,
                                'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
                                'timestamp' => date('Y-m-d H:i:s'),
                                'data' => session_encode()
                            ]
                        );

                        // Requested through API, provide the access token
                        return make_json([
                            'status' => 200,
                            'message' => phrase('You were signed in.'),
                            'access_token' => $session_id
                        ]);
                    } else {
                        // Send notification
                        $this->_send_notification($execute->user_id);

                        $referrer = service('request')->getUserAgent()->getReferrer();
                        $redirect = service('request')->getGet('redirect');

                        if (stripos($referrer, base_url()) !== false) {
                            $redirect = str_replace(base_url(), '', $referrer);
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

        $this->set_title(phrase('Dashboard Access'))
        ->set_icon('mdi mdi-lock-open-outline')
        ->set_description(phrase('Please enter your account information to signing in.'))

        ->set_output([
            'years' => $this->_get_active_years(),
            'activation' => $this->_get_activation()
        ])

        ->modal_size((get_setting('frontend_registration') ? 'modal-lg' : 'modal-md'))

        ->render();
    }

    /**
     * Sign out
     */
    public function sign_out()
    {
        /**
         * Prepare to revoke provider token
         */
        if (get_userdata('oauth_uid')) {
            // Retrieve service provider from sso uid
            $provider = $this->model->get_where(
                'app__users_oauth',
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
                $hybridauth = new \Hybridauth\Hybridauth($config);

                // Instantiate adapter directly
                $adapter = $hybridauth->authenticate($provider);

                // Disconnect the adapter (log out)
                $adapter->disconnect();
            } catch(\Throwable $e) {
                // Safe abstraction
            }
        }

        // Destroy session
        service('session')->destroy();

        return throw_exception(301, phrase('You were signed out.'), base_url(), true);
    }

    /**
     * Get active years
     */
    private function _get_active_years()
    {
        $output = [];

        $query = $this->model->get_where(
            'app__years',
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
    private function _get_activation()
    {
        if (! service('request')->getGet('activation')) {
            return false;
        }

        $user_id = 0;

        try {
            $user_id = service('encrypter')->decrypt(base64_decode(service('request')->getGet('activation')));
        } catch(\Throwable $e) {
            // Safe abstraction
        }

        if ($this->model->get_where('app__users_hashes', ['user_id' => $user_id], 1)->row()) {
            return true;
        }

        return false;
    }

    /**
     * Send notification
     */
    private function _send_notification($user_id = 0)
    {
        $query = $this->model->get_where(
            'app__users',
            [
                'user_id' => $user_id
            ],
            1
        )
        ->row();

        if ($query) {
            $messaging = new \Aksara\Libraries\Messaging();

            $messaging->set_email($query->email)
            ->set_phone($query->phone)
            ->set_subject(phrase('Login Activity'))
            ->set_message(
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
