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

use Aksara\Laboratory\Core;
use Aksara\Libraries\WebAuthn;

class Passkey extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate assertion login options
     */
    public function options()
    {
        $passkeys = $this->model->get('app_users_passkeys')->result();

        $webAuthn = new WebAuthn();
        $options = $webAuthn->generateLoginOptions($passkeys);

        return make_json([
            'status' => 200,
            'options' => $options
        ]);
    }

    /**
     * Verify assertion login response and sign user in
     */
    public function verify()
    {
        $payload = $this->request->getJSON(true);
        if (! $payload) {
            $payload = $this->request->getPost();
        }

        if (empty($payload['id'])) {
            return throw_exception(400, ['message' => phrase('Invalid Passkey payload.')]);
        }

        $challenge = get_userdata('_webauthn_login_challenge');

        if (! $challenge) {
            return throw_exception(400, ['message' => phrase('Passkey challenge expired. Please try again.')]);
        }

        $passkey = $this->model->getWhere(
            'app_users_passkeys',
            [
                'credential_id' => $payload['id']
            ],
            1
        )
            ->row();

        if (! $passkey) {
            return throw_exception(400, ['message' => phrase('Passkey credential not found or removed.')]);
        }

        $webAuthn = new WebAuthn();
        $verified = $webAuthn->verifyLogin($payload, $challenge, $passkey);

        if (! $verified) {
            return throw_exception(400, ['message' => phrase('Passkey authentication failed.')]);
        }

        // Fetch user record
        $user = $this->model->getWhere(
            'app_users',
            [
                'user_id' => $passkey->user_id
            ],
            1
        )
            ->row();

        if (! $user || 1 != $user->status) {
            return throw_exception(400, ['message' => phrase('Your account is temporarily disabled or not yet activated.')]);
        }

        // Regenerate session ID
        service('session')->regenerate();

        $secureToken = bin2hex(random_bytes(32));

        // Set user credential into session
        set_userdata([
            'is_logged' => true,
            'user_id' => $user->user_id,
            'username' => $user->username,
            'group_id' => $user->group_id,
            'language_id' => (get_userdata('language_id') ? get_userdata('language_id') : $user->language_id),
            'session_generated' => time(),
            'access_token' => $secureToken
        ]);

        // Update timestamps
        $this->model->update(
            'app_users_passkeys',
            [
                'last_used_at' => date('Y-m-d H:i:s'),
                'sign_count' => $passkey->sign_count + 1
            ],
            [
                'id' => $passkey->id
            ]
        );

        $this->model->update(
            'app_users',
            [
                'last_login' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $user->user_id
            ]
        );

        $this->model->insert(
            'app_sessions',
            [
                'id' => get_userdata('access_token'),
                'ip_address' => $this->request->getIPAddress(),
                'timestamp' => date('Y-m-d H:i:s'),
                'data' => (DB_DRIVER === 'Postgre' ? '\x' . bin2hex(session_encode()) : session_encode())
            ]
        );

        unset_userdata('_webauthn_login_challenge');

        $redirect = $this->request->getGet('redirect');

        if (! $redirect) {
            $redirect = 'dashboard';
        }

        return throw_exception(301, phrase('Welcome back') . ', <b>' . get_userdata('first_name') . '</b>! ' . phrase('You have been signed in successfully with Passkey.'), base_url($redirect), true);
    }
}
