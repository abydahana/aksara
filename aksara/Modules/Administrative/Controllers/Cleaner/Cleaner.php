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

namespace Aksara\Modules\Administrative\Controllers\Cleaner;

use Throwable;
use Aksara\Laboratory\Core;

class Cleaner extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->setPermission();

        $this->setTheme('backend');

        $this->searchable(false);
    }

    public function index()
    {
        $this->setTitle(phrase('Session Garbage Cleaner'))
        ->setIcon('mdi mdi-trash-can')

        ->render();
    }

    /**
     * Clean unused session
     */
    public function clean()
    {
        $this->permission->mustAjax();

        /**
         * Clean session garbage
         */
        $sessionDriver = (config('Session')->driver ? config('Session')->driver : '');
        $sessionExpiration = config('Session')->expiration;
        $sessionPath = config('Session')->savePath;
        $sessionCleaned = 0;
        $error = false;

        if (stripos($sessionDriver, 'file') !== false) {
            // File session handler
            if (is_writable($sessionPath)) {
                helper('filesystem');

                $session = directory_map($sessionPath);

                if ($session) {
                    foreach ($session as $key => $val) {
                        $modifiedTime = filemtime($sessionPath . DIRECTORY_SEPARATOR . $val);

                        if ('index.html' == $val || ! is_file($sessionPath . DIRECTORY_SEPARATOR . $val) || ! $modifiedTime || $modifiedTime > (time() - $sessionExpiration)) {
                            continue;
                        }

                        try {
                            if (unlink($sessionPath . DIRECTORY_SEPARATOR . $val)) {
                                $sessionCleaned++;
                            }
                        } catch (Throwable $e) {
                            // Safe abstraction
                        }
                    }
                }
            } else {
                $error = phrase('The session save path is not writable!');
            }
        } elseif (stripos($sessionDriver, 'database') !== false) {
            // Database session handler
            if ('Postgre' == DB_DRIVER) {
                $this->model->where('extract(epoch from timestamp) < ', (time() - $sessionExpiration));
            } else {
                $this->model->where('timestamp < ', (time() - $sessionExpiration));
            }

            $this->model->delete($sessionPath);

            $sessionCleaned = $this->model->affectedRows();
        }

        if ($error) {
            // Throw with error
            return throw_exception(403, $error, go_to());
        } elseif ($sessionCleaned > 0) {
            // Throw with amount of cleaned garbage
            $html = '
                <div class="text-center">
                    <i class="mdi mdi-delete-empty mdi-5x text-success"></i>
                    <h5>
                        ' . phrase('Garbage Cleaned!') . '
                    </h5>
                    <p>
                        ' . phrase('There are {{sessions}} unused sessions were cleaned up successfully.', ['sessions' => number_format($sessionCleaned)]) . '
                    </p>
                    <a href="javascript:void(0)" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                        <i class="mdi mdi-window-close"></i>
                        ' . phrase('Close') . '
                        <em class="text-sm">(esc)</em>
                    </a>
                </div>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'popup' => true,
                    'modal_size' => 'modal-sm'
                ],
                'content' => $html
            ]);
        }

        // No garbage found
        return throw_exception(301, phrase('There are no session garbage available at the moment.'), go_to());
    }
}
