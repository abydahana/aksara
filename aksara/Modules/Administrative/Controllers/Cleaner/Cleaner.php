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
    }

    public function index()
    {
        $this->setTitle(phrase('System Cleaner'))
        ->setIcon('mdi mdi-trash-can')

        ->render();
    }

    /**
     * Clean unused session
     */
    public function session()
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
                        ' . phrase('{{sessions}} unused sessions were cleaned up successfully.', ['sessions' => number_format($sessionCleaned)]) . '
                    </p>
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm rounded-pill" data-bs-dismiss="modal">
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

    /**
     * Clear application cache
     */
    public function cache()
    {
        $this->permission->mustAjax();

        try {
            $this->_clearCacheDirectory(WRITEPATH . 'cache');
        } catch (Throwable $e) {
            return throw_exception(403, phrase('Unable to clear application cache.'), go_to());
        }

        $html = '
            <div class="text-center">
                <i class="mdi mdi-cached mdi-5x text-success"></i>
                <h5>
                    ' . phrase('Cache Cleared!') . '
                </h5>
                <p>
                    ' . phrase('Application cache was cleared successfully.') . '
                </p>
                <a href="javascript:void(0)" class="btn btn-primary btn-sm rounded-pill" data-bs-dismiss="modal">
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

    /**
     * Clear writable/cache contents while keeping its root index.html file.
     */
    private function _clearCacheDirectory(string $path): void
    {
        if (! is_dir($path) || ! is_writable($path)) {
            throw new \RuntimeException('Cache directory is not writable.');
        }

        $items = scandir($path);

        if (! $items) {
            return;
        }

        foreach ($items as $item) {
            if (in_array($item, ['.', '..', 'index.html'])) {
                continue;
            }

            $this->_deleteCacheItem($path . DIRECTORY_SEPARATOR . $item);
        }
    }

    /**
     * Delete a file, symlink, or directory recursively.
     */
    private function _deleteCacheItem(string $path): void
    {
        if (is_link($path) || is_file($path)) {
            try {
                set_error_handler(static function (int $severity, string $message, string $file, int $line) {
                    throw new \ErrorException($message, 0, $severity, $file, $line);
                });

                $deleted = unlink($path);
            } catch (Throwable $e) {
                throw new \RuntimeException('Unable to remove cache file.', 0, $e);
            } finally {
                restore_error_handler();
            }

            if (! $deleted && file_exists($path)) {
                throw new \RuntimeException('Unable to remove cache file.');
            }

            return;
        }

        if (! is_dir($path)) {
            return;
        }

        $items = scandir($path);

        if ($items) {
            foreach ($items as $item) {
                if (in_array($item, ['.', '..'])) {
                    continue;
                }

                $this->_deleteCacheItem($path . DIRECTORY_SEPARATOR . $item);
            }
        }

        try {
            set_error_handler(static function (int $severity, string $message, string $file, int $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });

            $deleted = rmdir($path);
        } catch (Throwable $e) {
            throw new \RuntimeException('Unable to remove cache directory.', 0, $e);
        } finally {
            restore_error_handler();
        }

        if (! $deleted && is_dir($path)) {
            throw new \RuntimeException('Unable to remove cache directory.');
        }
    }
}
