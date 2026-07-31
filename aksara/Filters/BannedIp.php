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

namespace Aksara\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BannedIp implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $ip = $request->getIPAddress();
        $banFile = $this->getBanFile($ip);
        $banData = $this->getBanData($banFile);

        /*
         * Allow the request when no active ban exists.
         */
        if (null === $banData) {
            return null;
        }

        return service('response')
            ->setStatusCode(403)
            ->setContentType('text/html')
            ->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0'
            )
            ->setHeader('Pragma', 'no-cache')
            ->setBody(
                view('errors/html/banned', [
                    'expiresAt' => (int) $banData['expires_at'],
                ])
            );
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ): void {
        /*
         * No action is required after the response.
         */
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
     * Reads an active ban from disk.
     *
     * Expired or invalid files are removed automatically.
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

        $banData = json_decode($contents, true);

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
}
