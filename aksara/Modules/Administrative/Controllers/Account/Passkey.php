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

namespace Aksara\Modules\Administrative\Controllers\Account;

use Aksara\Laboratory\Core;
use Aksara\Libraries\WebAuthn;

class Passkey extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(301, phrase('Please sign in to access this page.'), base_url('auth'), true);
        }

        $passkeys = $this->model->getWhere(
            'app_users_passkeys',
            [
                'user_id' => get_userdata('user_id')
            ]
        )->result();

        $this->setTitle(phrase('Passkey Security'))
        ->setIcon('mdi mdi-fingerprint')
        ->setOutput([
            'passkeys' => $passkeys
        ])
        ->render();
    }

    /**
     * Generate registration challenge options for logged-in user
     */
    public function register()
    {
        $this->permission->mustAjax(go_to());

        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('You are not authorized to perform this action.'));
        }

        $user = $this->model->getWhere(
            'app_users',
            [
                'user_id' => get_userdata('user_id')
            ],
            1
        )->row();

        if (! $user) {
            return throw_exception(404, phrase('User record not found.'));
        }

        $existingPasskeys = $this->model->getWhere(
            'app_users_passkeys',
            [
                'user_id' => get_userdata('user_id')
            ]
        )->result();

        $webAuthn = new WebAuthn();
        $options = $webAuthn->generateRegisterOptions($user, $existingPasskeys);

        return make_json([
            'status' => 200,
            'options' => $options
        ]);
    }

    /**
     * Verify attestation response and store new passkey credential
     */
    public function verify()
    {
        $this->permission->mustAjax(go_to());

        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('You are not authorized to perform this action.'));
        }

        $payload = $this->request->getJSON(true);
        if (! $payload) {
            $payload = $this->request->getPost();
        }

        $challenge = get_userdata('_webauthn_register_challenge');
        if (! $challenge) {
            return throw_exception(400, ['message' => phrase('Passkey registration session expired. Please try again.')]);
        }

        $webAuthn = new WebAuthn();
        $verified = $webAuthn->verifyRegistration($payload, $challenge);

        if (! $verified) {
            return throw_exception(400, ['message' => phrase('Passkey registration verification failed.')]);
        }

        $deviceName = ! empty($payload['device_name']) ? trim($payload['device_name']) : phrase('My Passkey Device');

        $inserted = $this->model->insert(
            'app_users_passkeys',
            [
                'user_id' => get_userdata('user_id'),
                'credential_id' => $verified['credential_id'],
                'public_key' => $verified['public_key'],
                'transports' => $verified['transports'],
                'device_name' => $deviceName,
                'created_at' => date('Y-m-d H:i:s'),
                'last_used_at' => null
            ]
        );

        unset_userdata('_webauthn_register_challenge');

        if ($inserted) {
            return throw_exception(301, phrase('Passkey device successfully added to your account.'), go_to());
        }

        return throw_exception(500, phrase('Failed to save passkey credential to database.'));
    }

    /**
     * Delete a registered passkey
     */
    public function delete($id = 0)
    {
        $this->permission->mustAjax(go_to());

        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('You are not authorized to perform this action.'));
        }

        $passkeyId = $id ? $id : $this->request->getPost('id');

        $passkey = $this->model->getWhere(
            'app_users_passkeys',
            [
                'id' => $passkeyId,
                'user_id' => get_userdata('user_id')
            ],
            1
        )->row();

        if (! $passkey) {
            return throw_exception(404, phrase('Passkey credential not found.'));
        }

        $this->model->delete(
            'app_users_passkeys',
            [
                'id' => $passkeyId,
                'user_id' => get_userdata('user_id')
            ]
        );

        return throw_exception(301, phrase('Passkey credential successfully removed.'), go_to());
    }
}
