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

namespace Aksara\Modules\Administrative\Controllers\Updater;

class Updater extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();
        $this->set_permission();
        $this->set_theme('backend');
    }

    /**
     * Ping upstream
     */
    public static function ping_upstream($changelog = false)
    {
        if (! function_exists('curl_init') || ! function_exists('curl_exec') || ! @fsockopen('www.aksaracms.com', 443)) {
            return false;
        }

        try {
            $curl = \Config\Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false
            ]);

            $response = $curl->post(
                'https://www.aksaracms.com/updater/ping',
                [
                    'allow_redirects' => [
                        'max' => 2
                    ],
                    'headers' => [
                        'Referer' => base_url()
                    ],
                    'form_params' => [
                        'version' => aksara('version'),
                        'build_version' => aksara('build_version'),
                        'changelog' => $changelog
                    ]
                ]
            );
        } catch(\Throwable $e) {
            $response = null;
        }

        if ($response) {
            return json_decode($response->getBody());
        }

        return false;
    }

    public function index()
    {
        if ($this->valid_token(service('request')->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(403, phrase('Changes will not saved in demo mode!'), current_page());
            }

            if (! function_exists('curl_init') || ! function_exists('curl_exec') || ! @fsockopen('www.aksaracms.com', 443)) {
                return false;
            }

            try {
                $curl = \Config\Services::curlrequest([
                    'timeout' => 5,
                    'http_errors' => false
                ]);

                $response = $curl->post(
                    'https://www.aksaracms.com/updater/update',
                    [
                        'allow_redirects' => [
                            'max' => 2
                        ],
                        'headers' => [
                            'Referer' => base_url()
                        ],
                        'form_params' => [
                            'version' => aksara('version'),
                            'build_version' => aksara('build_version')
                        ]
                    ]
                );

                $response = json_decode($response->getBody());
            } catch(\Throwable $e) {
                $response = null;
            }

            if ($response) {
                // Run updater
                $this->_run_updater($response);
            }

            return throw_exception(404, phrase('No update are available at the moment.'), current_page());
        }

        $this->set_title(phrase('Core System Updater'))
        ->set_icon('mdi mdi-update')

        ->set_output([
            'updater' => $this->ping_upstream(true)
        ])

        ->render();
    }

    /**
     * Run instant updater
     */
    private function _run_updater($response = [])
    {
        $updater_path = sha1($response->version);
        $tmp_path = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $updater_path;
        $old_dependencies = json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);
        $backup_name = '_BACKUP_' . date('Y-m-d_His', time()) . '.zip';
        $zip = new \ZipArchive();

        /**
         * Create backup file
         */
        try {
            if (! is_dir($tmp_path)) {
                mkdir($tmp_path, 0755, true);
            }

            $zip->open($tmp_path . DIRECTORY_SEPARATOR . $backup_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $zip->addFile(ROOTPATH . 'composer.json', 'composer.json');
            $zip->addFile(ROOTPATH . 'composer.lock', 'composer.lock');

            $files = new \AppendIterator();
            $files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'aksara'), \RecursiveIteratorIterator::LEAVES_ONLY));
            $files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'public'), \RecursiveIteratorIterator::LEAVES_ONLY));
            $files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'themes'), \RecursiveIteratorIterator::LEAVES_ONLY));

            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (! $file->isDir()) {
                    // Add current file to archive
                    $zip->addFile($file->getRealPath(), str_replace(ROOTPATH, '', $file->getRealPath()));
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
        } catch(\Throwable $e) {
            // Remove temporary path
            $this->_rmdir($tmp_path);

            return throw_exception(400, ['package' => phrase('Update canceled to inability to write the backup file!') . ': ' . $e->getMessage()]);
        }

        $updater_package = null;
        $updated = false;

        try {
            // Get update package from the remote server
            copy($response->updater, $tmp_path . DIRECTORY_SEPARATOR . $response->version . '.zip');

            /**
             * STEP 1
             * open and extract the updater file to the temporary directory to get the updater files
             */
            if ($zip->open($tmp_path . DIRECTORY_SEPARATOR . $response->version . '.zip') === true && $zip->extractTo($tmp_path . DIRECTORY_SEPARATOR)) {
                // Close the opened zip
                $zip->close();

                // Set the updater name
                $updater_name = 'aksara-' . $response->version;

                // Create recursive directory iterator
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tmp_path . DIRECTORY_SEPARATOR . $updater_name), \RecursiveIteratorIterator::LEAVES_ONLY);

                // Create updater package
                $zip->open($tmp_path . DIRECTORY_SEPARATOR . $response->version . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                // Initialize updater file collections
                foreach ($files as $name => $file) {
                    // Skip empty directory
                    if (! $file->isDir()) {
                        // Add current file to archive
                        $zip->addFile($file->getRealPath(), substr($file->getRealPath(), strlen($tmp_path . DIRECTORY_SEPARATOR . $updater_name) + 1));
                    }
                }

                // Close the opened zip
                $zip->close();
            }

            /**
             * STEP 2
             * extract created updater file to root of the Aksara installation
             */
            if ($zip->open($tmp_path . DIRECTORY_SEPARATOR . $response->version . '.zip') === true && $zip->extractTo(ROOTPATH)) {
                // Updater success, change the state
                $updated = true;

                // Close the opened zip
                $zip->close();
            }
        } catch(\Throwable $e) {
            // Extract failed, revert the updater
            return throw_exception(400, ['package' => $e->getMessage()]);
        }

        if ($updated) {
            // Extract the dependencies
            $new_dependencies = json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);

            if (isset($old_dependencies['require']) && isset($new_dependencies['require'])) {
                // Find the dependencies difference
                $dependency_updated = array_diff($old_dependencies['require'], $new_dependencies['require']);

                // Merge dependencies
                $new_dependencies['require'] = array_unique(array_merge($old_dependencies['require'], $new_dependencies['require']));
            }

            // Database migrations and seeder
            try {
                // Remove temporary path
                $this->_rmdir($tmp_path);

                // Update and merge the dependencies
                file_put_contents(ROOTPATH . 'composer.json', json_encode($new_dependencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                // Run the updater migration
                $migration = \Config\Services::migrations()->setNamespace('Aksara');

                // Migrate the updater database schema
                if ($migration->latest()) {
                    // Call seeder
                    $seeder = \Config\Database::seeder();

                    // Run seeder
                    $seeder->call('Aksara\Database\Seeds\Updater');
                }

                $html = '
                    <div class="text-center mb-3">
                        <i class="mdi mdi-arrow-up-circle-outline mdi-5x text-success"></i>
                        <br />
                        <h5>
                            ' . phrase('Your core system has been successfully updated!') . '
                        </h5>
                    </div>
                    ' . ($dependency_updated ? '
                    <div class="alert alert-warning text-sm border-0 rounded-0 row">
                        <div class="text-center">
                            ' . phrase('You may need to run the composer update from the directory below to update the dependencies') . ':
                            <br />
                            <code>' . ROOTPATH . '</code>
                        </div>
                    </div>
                    ' : null) . '
                    ' . (is_dir($tmp_path) ? '
                    <div class="alert alert-warning text-sm border-0 rounded-0 row">
                        <div class="text-center">
                            ' . phrase('Unable to remove the updater junk files from the cache directory') . ':
                            <br />
                            <code>' . $tmp_path . '</code>
                        </div>
                    </div>
                    ' : null) . '
                    <p>
                        ' . phrase('If you found a problem after the update, please consider to open an issue to our official support.') . '
                    </p>
                    <ul>
                        <li>
                            <a href="//github.com/abydahana/aksara/issues" class="fw-bold" target="_blank">
                                GitHub Issue
                            </a>
                        </li>
                        <li>
                            <a href="//aksaracms.com/forum" class="fw-bold" target="_blank">
                                Aksara Forum
                            </a>
                        </li>
                    </ul>
                    <p class="text-center">
                        ' . phrase('You will be notified when another update is available.') . ' ' . phrase('Keep in mind that we are collect the donation from people like you to support our research.') . ' ' . phrase('We look forward to your contributions either kind of donations or development.') . '
                    </p>
                    <div class="text-center">
                        <a href="//abydahana.github.io" target="_blank">
                            <h5>
                                <i class="mdi mdi-heart text-danger"></i>
                                Aby Dahana
                            </h5>
                        </a>
                    </div>
                    <hr class="row" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="//www.aksaracms.com/pages/about/donation" class="btn btn-light" target="_blank">
                                    <i class="mdi mdi-launch"></i>
                                    ' . phrase('Donate') . '
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="' . current_page() . '" class="btn btn-primary">
                                    <i class="mdi mdi-reload"></i>
                                    ' . phrase('Refresh') . '
                                </a>
                            </div>
                        </div>
                    </div>
                ';

                return make_json([
                    'status' => 200,
                    'meta' => [
                        'title' => phrase('Update Success'),
                        'icon' => 'mdi mdi-check',
                        'popup' => true
                    ],
                    'content' => $html
                ]);
            } catch(\Throwable $e) {
                // Update failed
                return throw_exception(400, ['upgrade' => $e->getMessage()]);
            }
        }

        try {
            // Update failed, restore the backup file
            if ($zip->open($tmp_path . DIRECTORY_SEPARATOR . $backup_name) === true && $zip->extractTo(ROOTPATH)) {
                // Close the opened zip
                $zip->close();
            }
        } catch(\Throwable $e) {
            // Backup file restore failed
            return throw_exception(400, ['upgrade' => $e->getMessage()]);
        }

        $html = '
            <div class="mb-3">
                <div class="text-center">
                    <i class="mdi mdi-block-helper mdi-5x text-danger"></i>
                    <br />
                    <h5>
                        ' . phrase('Update failed due inability to write the updater file.') . ' ' . phrase('Please use the manual update instead.') . '
                    </h5>
                </div>
            </div>
            <ol>
                <li>
                    ' . phrase('Download the updater file') . '
                </li>
                <li>
                    ' . phrase('Extract the updater file contents to the following directory:') . '
                    <br />
                    <code>' . ROOTPATH . '</code>
                </li>
                <li>
                    ' . phrase('You may need to run the composer update from the directory below to update the dependencies:') . '
                    <br />
                    <code>' . ROOTPATH . '</code>
                </li>
            </ol>
            <br />
            <p>
                ' . phrase('If you found a problem after the update please consider to open an issue to our official support.') . '
            </p>
            <ul>
                <li>
                    <a href="//github.com/abydahana/aksara/issues" class="fw-bold" target="_blank">
                        GitHub Issue
                    </a>
                </li>
                <li>
                    <a href="//aksaracms.com/discuss" class="fw-bold" target="_blank">
                        Aksara Forum
                    </a>
                </li>
            </ul>
            <hr class="row" />
            <div class="row">
                <div class="col-6">
                    <div class="d-grid">
                        <a href="javascript:void(0)" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="mdi mdi-window-close"></i>
                            ' . phrase('Close') . '
                        </a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-grid">
                        <a href="//www.aksaracms.com/updater/file.zip" class="btn btn-dark">
                            <i class="mdi mdi-download"></i>
                            ' . phrase('Download Updater') . '
                        </a>
                    </div>
                </div>
            </div>
        ';

        return make_json([
            'status' => 200,
            'meta' => [
                'title' => phrase('Update Failed'),
                'icon' => 'mdi mdi-block-helper',
                'popup' => true
            ],
            'content' => $html
        ]);
    }

    /**
     * Remove directory recursivelly using
     *
     * @param   mixed|null $directory
     */
    private function _rmdir($directory = null)
    {
        if (is_dir($directory)) {
            // Delete directory
            if (! delete_files($directory, true)) {
                // Unable to delete directory. Get FTP configuration
                $site_id = get_setting('id');

                $query = $this->model->get_where(
                    'app__ftp',
                    [
                        'site_id' => $site_id
                    ],
                    1
                )
                ->row();

                if ($query) {
                    // Configuration found, decrypt password
                    $query->username = service('encrypter')->decrypt(base64_decode($query->username));
                    $query->password = service('encrypter')->decrypt(base64_decode($query->password));

                    try {
                        // Attempt to delete directory using ftp instead
                        $connection = ftp_connect($query->hostname, $query->port, 10);

                        if ($connection && ftp_login($connection, $query->username, $query->password)) {
                            // Yay! FTP is connected, try to delete the directory
                            $this->_ftp_rmdir($connection, $directory);

                            // Close FTP connection
                            ftp_close($connection);
                        }
                    } catch(\Throwable $e) {
                        return throw_exception(400, ['updater' => $e->getMessage()]);
                    }
                }
            } elseif (is_dir($directory)) {
                // Remove garbage directory
                rmdir($directory);
            }
        }
    }
}
