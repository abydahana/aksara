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

namespace Aksara\Modules\Administrative\Controllers\Cleaner;

class Cleaner extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();

        $this->set_theme('backend');

        $this->searchable(false);
    }

    public function index()
    {
        $this->set_title(phrase('Session Garbage Cleaner'))
        ->set_icon('mdi mdi-trash-can')

        ->render();
    }

    /**
     * Clean unused session
     */
    public function clean()
    {
        $this->permission->must_ajax();

        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../'));
        }

        $error = false;

        /**
         * Clean session garbage
         */
        $session_driver = (config('Session')->driver ? config('Session')->driver : '');
        $session_name = config('Session')->cookieName;
        $session_expiration = config('Session')->expiration;
        $session_path = config('Session')->savePath;
        $session_match_ip = config('Session')->matchIP;
        $session_cleaned = 0;

        if (stripos($session_driver, 'file') !== false) {
            // File session handler
            if (is_writable($session_path)) {
                helper('filesystem');

                $session = directory_map($session_path);

                if ($session) {
                    foreach ($session as $key => $val) {
                        $modified_time = filemtime($session_path . DIRECTORY_SEPARATOR . $val);

                        if ('index.html' == $val || ! is_file($session_path . DIRECTORY_SEPARATOR . $val) || ! $modified_time || $modified_time > (time() - $session_expiration)) {
                            continue;
                        }

                        try {
                            if (unlink($session_path . DIRECTORY_SEPARATOR . $val)) {
                                $session_cleaned++;
                            }
                        } catch (\Throwable $e) {
                            // Safe abstraction
                        }
                    }
                }
            } else {
                $error = phrase('The session save path is not writable!');
            }
        } elseif (stripos($session_driver, 'database') !== false) {
            // Database session handler
            if ('Postgre' == DB_DRIVER) {
                $this->model->where('extract(epoch from timestamp) < ', (time() - $session_expiration));
            } else {
                $this->model->where('timestamp < ', (time() - $session_expiration));
            }

            $this->model->delete($session_path);

            $session_cleaned = $this->model->affected_rows();
        }

        if ($error) {
            // Throw with error
            return throw_exception(403, $error, go_to());
        } elseif ($session_cleaned > 0) {
            // Throw with amount of cleaned garbage
            $html = '
                <div class="text-center">
                    <i class="mdi mdi-delete-empty mdi-5x text-success"></i>
                    <h5>
                        ' . phrase('Garbage Cleaned!') . '
                    </h5>
                    <p>
                        ' . phrase('There are {{sessions}} unused sessions were cleaned up successfully.', ['sessions' => number_format($session_cleaned)]) . '
                    </p>
                    <a href="javascript:void(0)" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
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
