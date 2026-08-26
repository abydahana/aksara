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

namespace Aksara\Modules\Administrative\Controllers\Updater;

use AppendIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use ZipArchive;
use Config\Database;
use Config\Services;
use Aksara\Laboratory\Core;

class Updater extends Core
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
        if ($this->validToken($this->request->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(403, phrase('Changes will not saved in demo mode.'), go_to());
            }

            try {
                $curl = Services::curlrequest([
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

                if ($response) {
                    // Run updater
                    return $this->_runUpdater($response);
                }
            } catch (Throwable $e) {
                return throw_exception(500, $e->getMessage(), current_page());
            }

            return throw_exception(404, phrase('No update are available at the moment.'), current_page());
        }

        $this->setTitle(phrase('Core System Updater'))
        ->setIcon('mdi mdi-update')

        ->setOutput([
            'updater' => $this->pingUpstream(true)
        ])

        ->render();
    }

    /**
     * Run migration and seeder for Aksara database
     */
    public function migrate()
    {
        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), go_to());
        }

        try {
            // Run migrations for Aksara namespace
            $migration = Services::migrations()->setNamespace('Aksara');
            $migration->latest();

            // Run all seeders under aksara/Database/Seeds
            $this->_runSeeds();

            $html = '
                <div class="text-center mb-3">
                    <i class="mdi mdi-database-check mdi-5x text-success"></i>
                    <br />
                    <h5>
                        ' . phrase('Database migration and seeder executed successfully!') . '
                    </h5>
                    <p class="text-muted">
                        ' . phrase('All pending database migrations and seeders under Aksara namespace have been applied.') . '
                    </p>
                    <a href="javascript:void(0)" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="mdi mdi-check"></i> ' . phrase('OK') . '
                    </a>
                </div>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'title' => phrase('Migration & Seeder'),
                    'icon' => 'mdi mdi-database-check',
                    'popup' => true
                ],
                'content' => $html
            ]);
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage(), current_page());
        }
    }

    /**
     * Ping upstream
     */
    public static function pingUpstream($changelog = false)
    {
        try {
            $curl = Services::curlrequest([
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

            return json_decode($response->getBody());
        } catch (Throwable $e) {
            // Safe abstraction
        }

        return false;
    }

    /**
     * Run instant updater
     */
    private function _runUpdater(object $response)
    {
        // Early Pre-check: Validate update metadata and Public Key BEFORE backup & download
        if (empty($response->updater) || (empty($response->sha256) && empty($response->signature))) {
            return throw_exception(400, ['package' => phrase('Update canceled! Package signature or integrity check failed.')]);
        }

        $publicKey = trim((string) get_setting('aksara_public_key'));

        if (! empty($response->signature) && (empty($publicKey) || ! str_contains($publicKey, 'PUBLIC KEY'))) {
            return throw_exception(400, ['package' => phrase('Update canceled! Package signature or integrity check failed.')]);
        }

        $updaterPath = sha1($response->version);
        $updated = false;
        $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $updaterPath;
        $oldDependencies = json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);
        $dependencyUpdated = [];
        $backupName = '_BACKUP_' . date('Y-m-d_His', time()) . '.zip';
        $zip = new ZipArchive();

        /**
         * Create backup file
         */
        try {
            if (! is_dir($tmpPath)) {
                mkdir($tmpPath, 0755, true);
            }

            $zip->open($tmpPath . DIRECTORY_SEPARATOR . $backupName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile(ROOTPATH . 'composer.json', 'composer.json');
            $zip->addFile(ROOTPATH . 'composer.lock', 'composer.lock');

            $files = new AppendIterator();
            $files->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH . 'aksara'), RecursiveIteratorIterator::LEAVES_ONLY));
            $files->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH . 'public'), RecursiveIteratorIterator::LEAVES_ONLY));
            $files->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH . 'themes'), RecursiveIteratorIterator::LEAVES_ONLY));

            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (! $file->isDir()) {
                    // Add current file to archive
                    $zip->addFile($file->getRealPath(), str_replace(ROOTPATH, '', $file->getRealPath()));
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
        } catch (Throwable $e) {
            // Close zip
            $zip->close();

            // Remove temporary path
            $this->_rmdir($tmpPath);

            return throw_exception(400, ['package' => phrase('Update canceled due to inability to write the backup file!') . ': ' . $e->getMessage()]);
        }

        try {
            // Get update package from the remote server
            $downloadedZip = $tmpPath . DIRECTORY_SEPARATOR . $response->version . '.zip';
            copy($response->updater, $downloadedZip);

            // Verify digital signature and SHA-256 integrity of the update package before extraction
            if (! $this->_verifyPackageSignature($downloadedZip, $response)) {
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['package' => phrase('Update canceled! Package signature or integrity check failed.')]);
            }

            /**
             * STEP 1
             * Open and extract the updater file to the temporary directory to get the updater files
             */
            if ($zip->open($tmpPath . DIRECTORY_SEPARATOR . $response->version . '.zip') === true && $this->_extractZipArchive($zip, $tmpPath . DIRECTORY_SEPARATOR)) {
                // Close zip
                $zip->close();

                // Set the updater name
                $updaterName = 'aksara-' . $response->version;

                // Create recursive directory iterator
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpPath . DIRECTORY_SEPARATOR . $updaterName), RecursiveIteratorIterator::LEAVES_ONLY);

                // Create updater package
                $zip->open($tmpPath . DIRECTORY_SEPARATOR . $response->version . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

                // Initialize updater file collections
                foreach ($files as $name => $file) {
                    // Skip empty directory
                    if (! $file->isDir()) {
                        // Add current file to archive
                        $zip->addFile($file->getRealPath(), substr($file->getRealPath(), strlen($tmpPath . DIRECTORY_SEPARATOR . $updaterName) + 1));
                    }
                }

                // Close zip
                $zip->close();
            }

            /**
             * STEP 2
             * Extract created updater file to root of the Aksara installation
             */
            if ($zip->open($tmpPath . DIRECTORY_SEPARATOR . $response->version . '.zip') === true && $this->_extractZipArchive($zip, ROOTPATH)) {
                // Updater success, change the state
                $updated = true;

                // Close the opened zip
                $zip->close();
            }
        } catch (Throwable $e) {
            return throw_exception(400, ['package' => $e->getMessage()]);
        }

        if ($updated) {
            // Extract the dependencies
            $newDependencies = json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);

            if (isset($oldDependencies['require']) && isset($newDependencies['require'])) {
                // Find the dependencies difference
                $dependencyUpdated = array_diff($oldDependencies['require'], $newDependencies['require']);

                // Merge dependencies
                $newDependencies['require'] = array_unique(array_merge($oldDependencies['require'], $newDependencies['require']));
            }

            // Database migrations and seeder
            try {
                // Remove temporary path
                $this->_rmdir($tmpPath);

                // Update and merge the dependencies
                file_put_contents(ROOTPATH . 'composer.json', json_encode($newDependencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                // Run the updater migration
                $migration = Services::migrations()->setNamespace('Aksara');
                $migration->latest();

                // Run all seeders under aksara/Database/Seeds
                $this->_runSeeds();


                $html = '
                    <div class="text-center mb-3">
                        <i class="mdi mdi-arrow-up-circle-outline mdi-5x text-success"></i>
                        <br />
                        <h5>
                            ' . phrase('Your core system has been successfully updated!') . '
                        </h5>
                    </div>
                    ' . ($dependencyUpdated ? '
                    <div class="mx--3 alert alert-warning text-sm border-0 rounded-0">
                        <div class="text-center">
                            ' . phrase('You may need to run the composer update from the directory below to update the dependencies:') . '
                            <br />
                            <code>' . ROOTPATH . '</code>
                        </div>
                    </div>
                    ' : null) . '
                    ' . (is_dir($tmpPath) ? '
                    <div class="mx--3 alert alert-warning text-sm border-0 rounded-0">
                        <div class="text-center">
                            ' . phrase('Unable to remove the updater junk files from the cache directory:') . '
                            <br />
                            <code>' . $tmpPath . '</code>
                        </div>
                    </div>
                    ' : null) . '
                    <p>
                        ' . phrase('If you found a problem after the update, consider to open an issue to our official support.') . '
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
                    <hr class="mx--3" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="//www.aksaracms.com/pages/about/donation" class="btn btn-outline-secondary" target="_blank">
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
            } catch (Throwable $e) {
                // Update failed
                return throw_exception(400, ['upgrade' => $e->getMessage()]);
            }
        }

        try {
            // Update failed, restore the backup file
            if ($zip->open($tmpPath . DIRECTORY_SEPARATOR . $backupName) === true && $this->_extractZipArchive($zip, ROOTPATH)) {
                // Close the opened zip
                $zip->close();

                // Remove temporary path
                $this->_rmdir($tmpPath);
            }
        } catch (Throwable $e) {
            // Backup file restore failed
            return throw_exception(400, ['upgrade' => $e->getMessage()]);
        }

        $html = '
            <div class="mb-3">
                <div class="text-center">
                    <i class="mdi mdi-block-helper mdi-5x text-danger"></i>
                    <br />
                    <h5>
                        ' . phrase('Update failed due to inability to write the updater file.') . ' ' . phrase('Please use the manual update instead.') . '
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
                ' . phrase('If you found a problem after the update, consider to open an issue to our official support.') . '
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
            <hr class="mx--3" />
            <div class="row">
                <div class="col-6">
                    <div class="d-grid">
                        <a href="javascript:void(0)" class="btn btn-outline-secondary" data-bs-dismiss="modal">
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
            $directories = scandir($directory);

            foreach ($directories as $object) {
                if ('.' != $object && '..' != $object) {
                    if (is_dir($directory . DIRECTORY_SEPARATOR . $object) && ! is_link($directory . DIRECTORY_SEPARATOR . $object)) {
                        $this->_rmdir($directory . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($directory . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }

            rmdir($directory);
        }
    }

    /**
     * Check if zip entry path is safe for extraction.
     * @param null|mixed $entry
     */
    private function _isSafeZipEntry($entry = null)
    {
        if (! $entry || strpos($entry, "\0") !== false) {
            return false;
        }

        if (preg_match('/^(?:[\/\\]|[A-Za-z]:[\/\\])/', $entry)) {
            return false;
        }

        if (preg_match('/(?:^|[\/\\])\.\.([\/\\]|$)/', $entry)) {
            return false;
        }

        return true;
    }

    /**
     * Extract zip archive safely after validating all entries.
     */
    private function _extractZipArchive(ZipArchive $zip, string $destination): bool
    {
        for ($i = 0, $count = $zip->numFiles; $i < $count; $i++) {
            $entryName = $zip->getNameIndex($i);

            if (false === $entryName || ! $this->_isSafeZipEntry($entryName)) {
                return false;
            }
        }

        return $zip->extractTo($destination);
    }

    /**
     * Automatically scan and run all seeders under aksara/Database/Seeds
     */
    private function _runSeeds()
    {
        $seedFiles = glob(ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Seeds' . DIRECTORY_SEPARATOR . '*.php');

        if (! empty($seedFiles)) {
            $seeder = Database::seeder();

            foreach ($seedFiles as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $className = 'Aksara\\Database\\Seeds\\' . $filename;

                if (class_exists($className)) {
                    try {
                        $seeder->call($className);
                    } catch (Throwable $e) {
                        // Safe abstraction
                    }
                }
            }
        }
    }

    /**
     * Verify SHA-256 hash and RSA public key cryptographic signature of update package.
     */
    private function _verifyPackageSignature(string $zipPath, object $response): bool
    {
        if (! is_file($zipPath)) {
            return false;
        }

        // 1. Verify SHA-256 hash if provided
        if (! empty($response->sha256)) {
            $fileHash = hash_file('sha256', $zipPath);

            if (! hash_equals(strtolower($response->sha256), strtolower($fileHash))) {
                return false;
            }
        }

        // 2. Verify Cryptographic OpenSSL Signature if signature is provided
        if (! empty($response->signature)) {
            $signature = base64_decode($response->signature, true);
            $content = file_get_contents($zipPath);

            if (false === $signature || false === $content) {
                return false;
            }

            $publicKey = trim((string) get_setting('aksara_public_key'));

            if (! empty($publicKey) && str_contains($publicKey, 'PUBLIC KEY')) {
                return 1 === openssl_verify($content, $signature, $publicKey, OPENSSL_ALGO_SHA256);
            }

            return false;
        }

        // Require at least sha256 or signature to be present
        return ! empty($response->sha256) || ! empty($response->signature);
    }
}
