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

class Forgot extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Check if use is already signed in
        if (get_userdata('is_logged')) {
            return throw_exception(301, phrase('You were signed in.'), base_url('dashboard'), true);
        }

        if ($this->valid_token(service('request')->getPost('_token')) || ($this->api_client && service('request')->getServer('REQUEST_METHOD') == 'POST')) {
            return $this->_validate_form();
        }

        $this->set_title(phrase('Reset Password'))
        ->set_description(phrase('Reset your password and request new one.'))
        ->set_icon('mdi mdi-account-key-outline')

        ->render();
    }

    public function reset()
    {
        $query = $this->model->get_where(
            'app__users_hashes',
            [
                'hash' => service('request')->getGet('hash')
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
        }

        $this->set_title(phrase('Reset Password'))
        ->set_description(phrase('Change your password with a new one.'))
        ->set_icon('mdi mdi-account-key-outline')

        ->form_callback('_reset_password')

        ->render(null, 'reset');
    }

    private function _validate_form()
    {
        // Set validation rules
        $this->form_validation->setRule('username', phrase('Username or email'), 'required');

        // Validate form
        if ($this->form_validation->run(service('request')->getPost()) === false) {
            // Validation error
            return throw_exception(400, $this->form_validation->getErrors());
        }

        $query = $this->model->select('
            user_id,
            email,
            first_name,
            last_name,
            status
        ')
        ->where('username', service('request')->getPost('username'))
        ->or_where('email', service('request')->getPost('username'))
        ->get_where(
            'app__users',
            [
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(400, ['username' => phrase('The username or email you entered does not registered.')]);
        } elseif (! $query->status) {
            return throw_exception(400, ['username' => phrase('Your account is temporary disabled or not yet activated.')]);
        }

        $query = $this->model->get_where(
            'app__users',
            [
                'user_id' => $user_id
            ],
            1
        )
        ->row();

        if ($query) {
            $token = sha1($query->username . time());

            $messaging = new \Aksara\Libraries\Messaging();

            $messaging->set_email($query->email)
            ->set_phone($query->phone)
            ->set_subject(phrase('Request new password'))
            ->set_message('
                <p>
                    ' . phrase('Hi') . ', <b>' . $query->first_name . ' ' . $query->last_name . '</b>
                </p>
                <p>
                    ' . phrase('Someone is recently asked to reset the password for an account linked to your email.') . ' ' . phrase('Please click the button below to reset your password.') . '
                </p>
                <p>
                    <a href="' . current_page('reset', ['hash' => $token]) . '" style="background:#007bff; color:#fff; text-decoration:none; font-weight:bold; border-radius:6px; padding:5px 10px; line-height:3">
                        ' . phrase('Reset Password') . '
                    </a>
                </p>
                <p>
                    ' . phrase('If this action is not requested by yourself, you can just ignore this email.') . '
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

            // Delete previous password request
            $this->model->delete(
                'app__users_hashes',
                [
                    'user_id' => $query->user_id
                ]
            );

            // Insert new request
            $this->model->insert(
                'app__users_hashes',
                [
                    'user_id' => $query->user_id,
                    'hash' => $token
                ]
            );
        }

        return throw_exception(301, phrase('The password reset link has been sent to') . ' ' . $query->email, base_url('auth'));
    }

    public function _reset_password()
    {
        $this->form_validation->setRule('password', phrase('New Password'), 'required');
        $this->form_validation->setRule('confirm_password', phrase('Password Confirmation'), 'required|matches[password]');

        if ($this->form_validation->run(service('request')->getPost()) === false) {
            return throw_exception(400, $this->form_validation->getErrors());
        }

        $query = $this->model->select('
            app__users.user_id,
            app__users.email,
            app__users.first_name,
            app__users.last_name,
            app__users.status
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

        if (! $query) {
            return throw_exception(400, ['password' => phrase('Your password has been reset recently.')]);
        } elseif (! $query->status) {
            return throw_exception(400, ['password' => phrase('Your account is temporary disabled or not yet activated.')]);
        }

        $messaging = new \Aksara\Libraries\Messaging();

        $messaging->set_email($query->email)
        ->set_phone($query->phone)
        ->set_subject(phrase('Password Reset Successfully'))
        ->set_message('
            <p>
                ' . phrase('Hi') . ', <b>' . $query->first_name . ' ' . $query->last_name . '</b>
            </p>
            <p>
                ' . phrase('You have successfully reset your password.') . ' ' . phrase('Now you can sign in to our website using your new password.') . '
            </p>
            <p>
                ' . phrase('Please contact us directly if you still unable to signing in.') . '
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

        $this->model->update(
            'app__users',
            [
                'password' => password_hash(service('request')->getPost('password') . ENCRYPTION_KEY, PASSWORD_DEFAULT)
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

        return throw_exception(301, phrase('You have successfully reset your password.'), base_url('auth', ['hash' => null]));
    }
}
