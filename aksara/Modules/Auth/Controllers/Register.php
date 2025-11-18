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

class Register extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        // Check if user is already signed in
        if (get_userdata('is_logged')) {
            return throw_exception(301, phrase('You have been signed in.'), base_url('dashboard'), true);
        } elseif (! get_setting('frontend_registration')) {
            // Frontend registration is disabled
            return throw_exception(403, phrase('The registration is temporary disabled.'), base_url('auth'));
        }

        // Unlink old captcha if any
        if (get_userdata('captcha_file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file'))) {
            try {
                unlink(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file'));
            } catch (\Throwable $e) {
                // Safe abstraction
            }
        }
    }

    public function index()
    {
        // Validate token
        if ($this->valid_token(service('request')->getPost('_token'))) {
            // Token valid, validate form
            return $this->_validate_form();
        }

        $string = '123456789ABCDEF';
        $length = 6;
        $captcha = [];

        if (is_writable(UPLOAD_PATH)) {
            if (! is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha')) {
                try {
                    mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha', 755, true);
                } catch (\Throwable $e) {
                    // Safe abstraction
                }
            }

            if (is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha') && is_writable(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha')) {
                helper('captcha');

                $captcha = create_captcha([
                    'img_path' => UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR,
                    'img_url' => base_url(UPLOAD_PATH . '/captcha'),
                    'img_width' => 120,
                    'img_height' => 30,
                    'expiration' => 3600,
                    'word_length' => $length,
                    'pool' => $string,
                    'colors' => [
                        'background' => [52, 58, 64],
                        'border' => [52, 58, 64],
                        'grid' => [52, 58, 64],
                        'text' => [255, 255, 255]
                    ]
                ]);
            }
        }

        if (! $captcha) {
            $captcha = [
                'word' => substr(str_shuffle(str_repeat($string, ceil($length / strlen($string)))), 1, $length),
                'filename' => null
            ];
        }

        // Set captcha word into session, used to next validation
        set_userdata([
            'captcha' => $captcha['word'],
            'captcha_file' => $captcha['filename']
        ]);

        $this->set_output([
            'captcha' => [
                'image' => ($captcha['filename'] ? base_url(UPLOAD_PATH . '/captcha/' . $captcha['filename']) : null),
                'string' => (! $captcha['filename'] ? $captcha['word'] : null)
            ]
        ]);

        $this->set_title(phrase('Register an Account'))
        ->set_icon('mdi mdi-account-plus')
        ->set_description(phrase('Fill all the required fields below to register your account.'))

        ->render();
    }

    /**
     * Form validation
     */
    public function _validate_form()
    {
        if (DEMO_MODE) {
            // Restrict on demo mode
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), current_page());
        } elseif (! $this->valid_token(service('request')->getPost('_token'))) {
            // Invalid token
            return throw_exception(403, phrase('The token you submitted has been expired or you are trying to bypass it from the restricted source.'), current_page());
        }

        // Set validation rules
        $this->form_validation->setRule('first_name', phrase('First Name'), 'required|max_length[32]');
        $this->form_validation->setRule('last_name', phrase('Last Name'), 'max_length[32]');
        $this->form_validation->setRule('username', phrase('Username'), 'required|alpha_numeric|unique[app__users.username]');
        $this->form_validation->setRule('email', phrase('Email Address'), 'required|valid_email|unique[app__users.email]');
        $this->form_validation->setRule('phone', phrase('Phone Number'), 'required|min_length[8]|max_length[16]');
        $this->form_validation->setRule('password', phrase('Password'), 'required|min_length[6]');
        $this->form_validation->setRule('captcha', phrase('Bot Challenge'), 'required|regex_match[/' . get_userdata('captcha') . '/i]');

        // Run validation
        if ($this->form_validation->run(service('request')->getPost()) === false) {
            // Validation error
            return throw_exception(400, $this->form_validation->getErrors());
        }

        // Prepare the insert data
        $prepare = [
            'first_name' => service('request')->getPost('first_name'),
            'last_name' => service('request')->getPost('last_name'),
            'username' => service('request')->getPost('username'),
            'email' => service('request')->getPost('email'),
            'phone' => service('request')->getPost('phone'),
            'password' => password_hash(service('request')->getPost('password') . ENCRYPTION_KEY, PASSWORD_DEFAULT),
            'group_id' => (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3),
            'language_id' => (get_setting('app_language') > 0 ? get_setting('app_language') : 1),
            'registered_date' => date('Y-m-d'),
            'last_login' => date('Y-m-d H:i:s'),
            'status' => (get_setting('auto_active_registration') ? 1 : 0)
        ];

        // Insert user with safe checkpoint
        if ($this->model->insert('app__users', $prepare, 1)) {
            $prepare['user_id'] = $this->model->insert_id();

            // Unset stored captcha
            unset_userdata(['captcha', 'captcha_file']);

            if (get_setting('auto_active_registration')) {
                $default_membership_group = (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3);

                // Set the user credential into session
                set_userdata([
                    'user_id' => $prepare['user_id'],
                    'group_id' => $default_membership_group,
                    'language_id' => $prepare['language_id'],
                    'is_logged' => true
                ]);

                // Send welcome email
                $this->_send_welcome_email($prepare);

                // Return to previous page
                return throw_exception(301, phrase('Your account has been registered successfully.'), base_url((service('request')->getGet('redirect') ? service('request')->getGet('redirect') : 'dashboard')), true);
            } else {
                // Send activation email
                $this->_send_activation_email($prepare);

                // Send to client
                return throw_exception(301, phrase('Follow the link we sent to your email to activate your account.'), base_url('auth', ['activation' => base64_encode(service('encrypter')->encrypt($prepare['user_id']))]));
            }
        } else {
            return throw_exception(500, phrase('Unable to register your account.') . ' ' . phrase('Please try again later.'));
        }
    }

    public function activate()
    {
        $query = $this->model->select('
            app__users.user_id,
            app__users.username,
            app__users.group_id,
            app__users.language_id
        ')
        ->join(
            'app__users',
            'app__users.user_id = app__users_hashes.user_id'
        )
        ->get_where(
            'app__users_hashes',
            [
                'app__users_hashes.hash' => service('request')->getGet('hash')
            ],
            1
        )
        ->row();

        if ($query) {
            $this->model->update(
                'app__users',
                [
                    'status' => 1
                ],
                [
                    'user_id' => $query->user_id
                ]
            );

            $this->model->delete(
                'app__users_hashes',
                [
                    'user_id' => $query->user_id
                ]
            );

            // Set the user credential into session
            set_userdata([
                'user_id' => $query->user_id,
                'username' => $query->username,
                'group_id' => $query->group_id,
                'language_id' => $query->language_id,
                'is_logged' => true,
                'session_generated' => time()
            ]);

            return throw_exception(301, phrase('Your account has been successfully activated.'), base_url((service('request')->getGet('redirect') ? service('request')->getGet('redirect') : null)));
        } else {
            return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
        }
    }

    private function _send_activation_email($params = [])
    {
        // Create token
        $token = sha1($params['email'] . time());

        // Insert hashes
        $this->model->insert(
            'app__users_hashes',
            [
                'user_id' => $params['user_id'],
                'hash' => $token
            ]
        );

        $messaging = new \Aksara\Libraries\Messaging();

        $messaging->set_email($params['email'])
        ->set_subject(phrase('Account Activation') . ' - ' . get_setting('app_name'))
        ->set_message('
            <p>
                ' . phrase('Hi') . ', <b>' . $params['first_name'] . ' ' . $params['last_name'] . '</b>
            </p>
            <p>
                ' . phrase('You are recently register your account using this email on our website.') . ' ' . phrase('Your account need to be activated.') . ' ' . phrase('Click link below to activate your account.') . '
            </p>
            <p>
                <a href="' . current_page('activate', ['hash' => $token]) . '" style="background:#007bff; color:#fff; text-decoration:none; font-weight:bold; border-radius:6px; padding:5px 10px; line-height:3">
                    ' . phrase('Activate your account') . '
                </a>
            </p>
            <br />
            <br />
            <p>
                <b>
                    ' . get_setting('office_name') . '
                </b>
                <br />
                ' . get_setting('office_address') . '
                <br />
                ' . get_setting('office_phone') . '
            </p>
        ')
        ->send(true);
    }

    private function _send_welcome_email($params = [])
    {
        $messaging = new \Aksara\Libraries\Messaging();

        $messaging->set_email($params['email'])
        ->set_subject(phrase('Account Activated') . ' - ' . get_setting('app_name'))
        ->set_message('
            <p>
                ' . phrase('Hi') . ', <b>' . $params['first_name'] . ' ' . $params['last_name'] . '</b>
            </p>
            <p>
                <b>
                    ' . phrase('Congratulations!') . ',
                </b>
                <br />
                ' . phrase('Your account was successfully registered to our website and already been activated.') . ' ' . phrase('You can use your email or username to sign in to your dashboard.') . '
            </p>
            <br />
            <br />
            <p>
                <b>
                    ' . get_setting('office_name') . '
                </b>
                <br />
                ' . get_setting('office_address') . '
                <br />
                ' . get_setting('office_phone') . '
            </p>
        ')
        ->send(true);
    }
}
