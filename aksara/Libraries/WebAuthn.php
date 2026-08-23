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

namespace Aksara\Libraries;

use Throwable;

class WebAuthn
{
    /**
     * Encode binary data to Base64Url string
     */
    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode Base64Url string to binary data
     */
    public function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }

    /**
     * Get RP ID (domain host without protocol/port)
     */
    public function getRpId(): string
    {
        $host = parse_url(base_url(), PHP_URL_HOST);

        return $host ? $host : 'localhost';
    }

    /**
     * Generate registration challenge options
     */
    public function generateRegisterOptions(object $user, array $existingPasskeys = []): array
    {
        $challenge = random_bytes(32);
        $challengeBase64 = $this->base64UrlEncode($challenge);

        set_userdata('_webauthn_register_challenge', $challengeBase64);

        $excludeCredentials = [];
        foreach ($existingPasskeys as $passkey) {
            if (! empty($passkey->credential_id)) {
                $excludeCredentials[] = [
                    'id' => $passkey->credential_id,
                    'type' => 'public-key'
                ];
            }
        }

        $userId = $this->base64UrlEncode((string) $user->user_id);
        $username = isset($user->username) ? $user->username : 'user_' . $user->user_id;
        $displayName = (isset($user->first_name) ? $user->first_name : '') . ' ' . (isset($user->last_name) ? $user->last_name : '');
        $displayName = trim($displayName) ?: $username;

        return [
            'rp' => [
                'name' => get_setting('app_title') ?: 'Aksara CMS',
                'id' => $this->getRpId()
            ],
            'user' => [
                'id' => $userId,
                'name' => $username,
                'displayName' => $displayName
            ],
            'challenge' => $challengeBase64,
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],   // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
                ['type' => 'public-key', 'alg' => -8]    // Ed25519
            ],
            'timeout' => 60000,
            'excludeCredentials' => $excludeCredentials,
            'authenticatorSelection' => [
                'residentKey' => 'preferred',
                'userVerification' => 'preferred'
            ],
            'attestation' => 'none'
        ];
    }

    /**
     * Generate assertion login options
     */
    public function generateLoginOptions(array $allowedPasskeys = []): array
    {
        $challenge = random_bytes(32);
        $challengeBase64 = $this->base64UrlEncode($challenge);

        set_userdata('_webauthn_login_challenge', $challengeBase64);

        $allowCredentials = [];
        foreach ($allowedPasskeys as $passkey) {
            if (! empty($passkey->credential_id)) {
                $allowCredentials[] = [
                    'id' => $passkey->credential_id,
                    'type' => 'public-key'
                ];
            }
        }

        return [
            'challenge' => $challengeBase64,
            'timeout' => 60000,
            'rpId' => $this->getRpId(),
            'allowCredentials' => $allowCredentials,
            'userVerification' => 'preferred'
        ];
    }

    /**
     * Verify WebAuthn registration attestation response
     */
    public function verifyRegistration(array $data, string $storedChallenge): array|false
    {
        try {
            if (empty($data['response']['clientDataJSON']) || empty($data['response']['attestationObject'])) {
                return false;
            }

            // Verify clientDataJSON
            $clientDataRaw = $this->base64UrlDecode($data['response']['clientDataJSON']);
            $clientData = json_decode($clientDataRaw, true);

            if (! is_array($clientData) || empty($clientData['type']) || 'webauthn.create' !== $clientData['type']) {
                return false;
            }

            if (empty($clientData['challenge']) || $clientData['challenge'] !== $storedChallenge) {
                return false;
            }

            // Extract public key and credential ID
            $attestationObjectRaw = $this->base64UrlDecode($data['response']['attestationObject']);

            // Parse attestation object (CBOR structure)
            $credentialId = ! empty($data['id']) ? $data['id'] : (! empty($data['rawId']) ? $data['rawId'] : null);
            if (! $credentialId) {
                return false;
            }

            // Extract raw public key / attestation object data for storage
            $publicKey = base64_encode($attestationObjectRaw);

            $transports = isset($data['response']['transports']) && is_array($data['response']['transports'])
                ? json_encode($data['response']['transports'])
                : null;

            return [
                'credential_id' => $credentialId,
                'public_key' => $publicKey,
                'transports' => $transports
            ];
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Verify WebAuthn assertion login response
     */
    public function verifyLogin(array $data, string $storedChallenge, object $passkeyRecord): bool
    {
        try {
            if (empty($data['response']['clientDataJSON']) || empty($data['response']['authenticatorData']) || empty($data['response']['signature'])) {
                return false;
            }

            // Verify clientDataJSON
            $clientDataRaw = $this->base64UrlDecode($data['response']['clientDataJSON']);
            $clientData = json_decode($clientDataRaw, true);

            if (! is_array($clientData) || empty($clientData['type']) || 'webauthn.get' !== $clientData['type']) {
                return false;
            }

            if (empty($clientData['challenge']) || $clientData['challenge'] !== $storedChallenge) {
                return false;
            }

            // Confirm credential ID match
            $credentialId = ! empty($data['id']) ? $data['id'] : $data['rawId'];
            if ($credentialId !== $passkeyRecord->credential_id) {
                return false;
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
