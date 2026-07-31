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

use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class InvalidUriExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        $ip = $request->getIPAddress();
        $ipHash = hash('sha256', $ip);
        $cache = cache();

        $maxAttempts = $this->getMaxAttempts();
        $attemptWindow = $this->getAttemptWindow();
        $banDuration = $this->getBanDuration();

        $attemptKey = 'aksara_invalid_uri_attempt_' . $ipHash;
        $banFile = $this->getBanFile($ip);

        /*
         * Immediately display the banned page when an active ban
         * already exists for the current IP address.
         */
        $existingBan = $this->getBanData($banFile);

        if (null !== $existingBan) {
            $this->sendView(
                $response,
                403,
                'errors/html/banned',
                [
                    'expiresAt' => (int) $existingBan['expires_at'],
                ]
            );

            exit($exitCode);
        }

        $attemptData = $cache->get($attemptKey);

        if (! is_array($attemptData)) {
            $attemptData = [
                'attempts' => 0,
                'started_at' => time(),
            ];
        }

        $attemptData['attempts'] =
            (int) ($attemptData['attempts'] ?? 0) + 1;

        $attemptData['last_attempt_at'] = time();

        /*
         * Each invalid request refreshes the attempt window.
         */
        $cache->save(
            $attemptKey,
            $attemptData,
            $attemptWindow
        );

        /*
         * Ban the IP after the allowed number of attempts
         * has been exceeded.
         */
        if ($attemptData['attempts'] >= $maxAttempts) {
            $bannedAt = time();
            $expiresAt = $bannedAt + $banDuration;

            $banData = [
                'ip' => $ip,
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
                'reason' => 'Repeated invalid URI requests',
                'attempts' => $attemptData['attempts'],
                'banned_at' => $bannedAt,
                'expires_at' => $expiresAt,
            ];

            $saved = $this->saveBanData(
                $banFile,
                $banData
            );

            /*
             * Remove the attempt counter after the ban file has
             * been stored successfully.
             */
            if ($saved) {
                $cache->delete($attemptKey);
            }

            log_message(
                'warning',
                'IP {ip} was banned after {attempts} invalid URI requests. File saved: {saved}. URI: {uri}',
                [
                    'ip' => $ip,
                    'attempts' => $attemptData['attempts'],
                    'saved' => $saved ? 'yes' : 'no',
                    'uri' => $_SERVER['REQUEST_URI'] ?? '',
                ]
            );

            $this->sendView(
                $response,
                403,
                'errors/html/banned',
                [
                    'expiresAt' => $expiresAt,
                ]
            );

            exit($exitCode);
        }

        $remainingAttempts = max(
            0,
            $maxAttempts - $attemptData['attempts']
        );

        log_message(
            'warning',
            'Invalid URI attempt {attempts} from IP {ip}. Remaining attempts: {remaining}. URI: {uri}',
            [
                'ip' => $ip,
                'attempts' => $attemptData['attempts'],
                'remaining' => $remainingAttempts,
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
            ]
        );

        $this->sendView(
            $response,
            400,
            'errors/html/invalid_uri',
            [
                'attempts' => $attemptData['attempts'],
                'remainingAttempts' => $remainingAttempts,
            ]
        );

        exit($exitCode);
    }

    /**
     * Returns the configured maximum number of tolerated attempts.
     */
    private function getMaxAttempts(): int
    {
        if (! defined('INVALID_URI_MAX_ATTEMPTS')) {
            return 3;
        }

        return max(
            0,
            (int) constant('INVALID_URI_MAX_ATTEMPTS')
        );
    }

    /**
     * Returns the configured attempt window in seconds.
     */
    private function getAttemptWindow(): int
    {
        if (! defined('INVALID_URI_ATTEMPT_WINDOW')) {
            return 600;
        }

        return max(
            1,
            (int) constant('INVALID_URI_ATTEMPT_WINDOW')
        );
    }

    /**
     * Returns the configured ban duration in seconds.
     */
    private function getBanDuration(): int
    {
        if (! defined('INVALID_URI_BAN_DURATION')) {
            return 900;
        }

        return max(
            1,
            (int) constant('INVALID_URI_BAN_DURATION')
        );
    }

    /**
     * Returns the absolute ban file path for the given IP address.
     */
    private function getBanFile(string $ip): string
    {
        return WRITEPATH
            . 'cache'
            . DIRECTORY_SEPARATOR
            . 'banned'
            . DIRECTORY_SEPARATOR
            . hash('sha256', $ip)
            . '.json';
    }

    /**
     * Reads active ban data from disk.
     *
     * Invalid or expired files are removed automatically.
     */
    private function getBanData(string $banFile): ?array
    {
        if (! is_file($banFile)) {
            return null;
        }

        $contents = file_get_contents($banFile);

        if (false === $contents) {
            return null;
        }

        $banData = json_decode(
            $contents,
            true
        );

        if (! is_array($banData)) {
            @unlink($banFile);

            return null;
        }

        $expiresAt = (int) ($banData['expires_at'] ?? 0);

        if ($expiresAt <= time()) {
            @unlink($banFile);

            return null;
        }

        return $banData;
    }

    /**
     * Stores ban data as a JSON file.
     */
    private function saveBanData(
        string $banFile,
        array $banData
    ): bool {
        $directory = dirname($banFile);

        if (
            ! is_dir($directory)
            && ! mkdir($directory, 0755, true)
            && ! is_dir($directory)
        ) {
            log_message(
                'error',
                'Unable to create banned IP directory: {directory}',
                [
                    'directory' => $directory,
                ]
            );

            return false;
        }

        $json = json_encode(
            $banData,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
        );

        if (false === $json) {
            log_message(
                'error',
                'Unable to encode ban data for IP: {ip}',
                [
                    'ip' => $banData['ip'] ?? 'unknown',
                ]
            );

            return false;
        }

        return file_put_contents(
            $banFile,
            $json,
            LOCK_EX
        ) !== false;
    }

    /**
     * Sends an HTML view as the exception response.
     */
    private function sendView(
        ResponseInterface $response,
        int $statusCode,
        string $view,
        array $data = []
    ): void {
        $response
            ->setStatusCode($statusCode)
            ->setContentType('text/html')
            ->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0'
            )
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(
                view($view, $data)
            )
            ->send();
    }
}
