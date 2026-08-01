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

class MaintenanceMode implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        if (! defined('MAINTENANCE_MODE') || ! MAINTENANCE_MODE) {
            return null;
        }

        /** @var \CodeIgniter\HTTP\IncomingRequest $request */
        if ('1' === $request->getGet('maintenance_status')) {
            return service('response')
                ->setStatusCode(503)
                ->setJSON([
                    'maintenance' => true,
                ]);
        }

        return service('response')
            ->setStatusCode(503)
            ->setContentType('text/html')
            ->setHeader('Retry-After', '3600')
            ->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0'
            )
            ->setHeader('Pragma', 'no-cache')
            ->setBody(view('errors/html/maintenance'));
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
}
