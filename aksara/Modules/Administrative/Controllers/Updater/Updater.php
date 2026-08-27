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
     * Manual update via uploaded zip package
     */
    public function upload()
    {
        if (! $this->validToken($this->request->getPost('_token'))) {
            $html = '
                <form action="' . current_page() . '" method="POST" enctype="multipart/form-data" class="--validate-form">
                    <input type="hidden" name="_token" value="' . generate_csrf_token() . '">
                    <div class="alert alert-info rounded-3">
                        <i class="mdi mdi-information-outline"></i>
                        ' . phrase('Manual update allows you to upload an official Aksara update package (.zip). Only authentic, digitally signed packages from official releases are accepted.') . '
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">
                            ' . phrase('Update Package (.zip)') . '
                        </label>
                        <input type="file" name="file" class="form-control" accept=".zip" />
                    </div>
                    <hr class="mx--3 border-secondary" />
                    <div data-role="validation-callback"></div>
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-window-close"></i>
                                    ' . phrase('Cancel') . '
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check"></i>
                                    ' . phrase('Upload & Update') . '
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'title' => phrase('Manual Core System Update'),
                    'icon' => 'mdi mdi-upload',
                    'popup' => true
                ],
                'content' => $html
            ]);
        }

        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page('../'));
        }

        $this->formValidation->setRule('file', phrase('Update Package'), 'max_size[file,' . (MAX_UPLOAD_SIZE * 1024) . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip]|ext_in[file,zip]');

        if ($this->formValidation->run($this->request->getPost()) === false) {
            return throw_exception(400, $this->formValidation->getErrors());
        } elseif (empty($_FILES['file']['tmp_name'])) {
            return throw_exception(400, ['file' => phrase('No update package was chosen.')]);
        } elseif (! class_exists('ZipArchive')) {
            return throw_exception(400, ['file' => phrase('No zip extension found on your web server configuration.')]);
        }

        $zip = new ZipArchive();
        $uploadedZipPath = $_FILES['file']['tmp_name'];
        $unzip = $zip->open($uploadedZipPath);
        $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'manual_update_' . sha1($uploadedZipPath);

        if (true === $unzip) {
            if (! is_dir($tmpPath) && ! mkdir($tmpPath, 0755, true)) {
                return throw_exception(400, ['file' => phrase('Unable to extract your update package.')]);
            }

            // Validate the zip contents and extract safely
            if (! $this->_extractZipArchive($zip, $tmpPath)) {
                $zip->close();
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['file' => phrase('Unable to extract your update package. Zip entry failed security validation.')]);
            }

            $zip->close();

            // 1. Locate and extract target version from package files
            $targetVersion = $this->_getPackageVersion($tmpPath);

            if (! $targetVersion) {
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['file' => phrase('Invalid update package! Version information was missing in composer.json.')]);
            }

            // 2. Validate Version Sequence (No minor version skipping)
            $versionError = $this->_validateVersionSequence(aksara('version'), $targetVersion);

            if ($versionError) {
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['file' => $versionError]);
            }

            // 3. Cryptographic RSA Signature Verification for Manual Package
            if (! $this->_verifyManualPackageSignature($uploadedZipPath, $tmpPath)) {
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['file' => phrase('Update canceled! Package signature or integrity check failed. The uploaded file is not an authentic signed release from GitHub.')]);
            }

            // 4. Security Scan for Malicious PHP Code
            if (! $this->_scanPackageSecurity($tmpPath)) {
                $this->_rmdir($tmpPath);

                return throw_exception(400, ['file' => phrase('Update canceled! Malicious or unsafe PHP code was detected in the update package.')]);
            }

            // 5. Create backup before applying update
            $backupName = '_BACKUP_' . date('Y-m-d_His', time()) . '.zip';
            $backupZip = new ZipArchive();

            try {
                $backupZip->open($tmpPath . DIRECTORY_SEPARATOR . $backupName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                $backupZip->addFile(ROOTPATH . 'composer.json', 'composer.json');
                $backupZip->addFile(ROOTPATH . 'composer.lock', 'composer.lock');
                $backupZip->close();
            } catch (Throwable $e) {
                // Safe abstraction
            }

            // 6. Copy extracted update files to ROOTPATH
            $manifestPath = $this->_findFileInDirectory($tmpPath, 'composer.json') ?: $this->_findFileInDirectory($tmpPath, 'Common.php');
            $sourceDir = $manifestPath ? (basename(dirname($manifestPath)) === 'aksara' ? dirname(dirname($manifestPath)) : dirname($manifestPath)) : $tmpPath;
            $copySuccess = $this->_copyDirectory($sourceDir, ROOTPATH);

            if ($copySuccess) {
                // Run migration and seeds
                try {
                    $migration = Services::migrations()->setNamespace('Aksara');
                    $migration->latest();
                    $this->_runSeeds();
                } catch (Throwable $e) {
                    // Migration error safe handling
                }

                // Remove temporary path
                $this->_rmdir($tmpPath);

                return throw_exception(301, phrase('Core system was successfully updated to version {{ version }}.', ['version' => $targetVersion]), current_page('../'));
            }

            $this->_rmdir($tmpPath);

            return throw_exception(400, ['file' => phrase('Failed to copy update files to root path.')]);
        }

        return throw_exception(400, ['file' => phrase('Unable to open the uploaded zip update package.')]);
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
     * @param null|mixed $directory
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
    private function _isSafeZipEntry($entry = null, string $destination = ''): bool
    {
        if (! $entry || strpos($entry, "\0") !== false) {
            return false;
        }

        // Normalize slashes
        $entry = str_replace('\\', '/', $entry);

        // Reject absolute paths
        if (preg_match('/^(?:\/|[A-Za-z]:\/)/', $entry)) {
            return false;
        }

        // Reject directory traversal segments
        if (preg_match('/(?:^|\/)\.\.(?:\/|$)/', $entry)) {
            return false;
        }

        // Verify resolved real destination remains strictly inside destination directory
        if ($destination) {
            $destReal = realpath($destination);

            if ($destReal) {
                $targetPath = $destReal . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $entry);

                // If target exists (e.g. symlink check), ensure it resolves inside $destReal
                if (file_exists($targetPath)) {
                    $targetReal = realpath($targetPath);

                    if ($targetReal && 0 !== strpos($targetReal, $destReal . DIRECTORY_SEPARATOR) && $targetReal !== $destReal) {
                        return false;
                    }
                }
            }
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

            if (false === $entryName || ! $this->_isSafeZipEntry($entryName, $destination)) {
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

    /**
     * Validate update version sequence: prevent version skipping (e.g. 6.3.5 -> 6.5.0 is prohibited).
     */
    private function _validateVersionSequence(string $currentVersion, string $targetVersion): ?string
    {
        if (version_compare($targetVersion, $currentVersion, '<=')) {
            return phrase('The uploaded package version ({{ target }}) must be newer than your current system version ({{ current }}).', [
                'target' => $targetVersion,
                'current' => $currentVersion
            ]);
        }

        $currentParts = explode('.', $currentVersion);
        $targetParts = explode('.', $targetVersion);

        $currMajor = (int) ($currentParts[0] ?? 0);
        $currMinor = (int) ($currentParts[1] ?? 0);

        $targetMajor = (int) ($targetParts[0] ?? 0);
        $targetMinor = (int) ($targetParts[1] ?? 0);

        // Major version check: Cannot skip major versions (e.g. from 6.x to 8.x)
        if ($targetMajor > $currMajor + 1) {
            return phrase('Version step too large! You cannot skip major versions. Please update to version {{ expected }}.x first before updating to {{ target }}.', [
                'expected' => $currMajor + 1,
                'target' => $targetVersion
            ]);
        }

        // Minor version check within same major version: Cannot skip minor versions (e.g. 6.3.5 -> 6.5.0)
        if ($targetMajor === $currMajor && $targetMinor > $currMinor + 1) {
            $expectedMinor = $currMajor . '.' . ($currMinor + 1) . '.x';

            return phrase('Version step too large! You cannot skip minor versions. You are currently on {{ current }}, so you must update to {{ expected }} first before updating to {{ target }}.', [
                'current' => $currentVersion,
                'expected' => $expectedMinor,
                'target' => $targetVersion
            ]);
        }

        return null;
    }

    /**
     * Recursively search for a file by name inside a directory.
     */
    private function _findFileInDirectory(string $directory, string $filename): ?string
    {
        if (! is_dir($directory)) {
            return null;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && strtolower($file->getFilename()) === strtolower($filename)) {
                return $file->getRealPath();
            }
        }

        return null;
    }

    /**
     * Verify digital RSA signature of manually uploaded update package.
     */
    private function _verifyManualPackageSignature(string $uploadedZipPath, string $tmpPath): bool
    {
        $publicKey = trim((string) get_setting('aksara_public_key'));

        if (empty($publicKey) || ! str_contains($publicKey, 'PUBLIC KEY')) {
            return false;
        }

        // Search for signature.txt inside extracted directory
        $sigPath = $this->_findFileInDirectory($tmpPath, 'signature.txt');
        $sigBase64 = null;

        if ($sigPath && file_exists($sigPath)) {
            $sigBase64 = trim(file_get_contents($sigPath));
        }

        if (! $sigBase64) {
            $manifestPath = $this->_findFileInDirectory($tmpPath, 'composer.json');
            if ($manifestPath && file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath));
                if (isset($manifest->signature)) {
                    $sigBase64 = trim((string) $manifest->signature);
                }
            }
        }

        if (! $sigBase64) {
            return false;
        }

        $rawSig = base64_decode($sigBase64, true);
        $zipContent = file_get_contents($uploadedZipPath);

        if (false === $rawSig || false === $zipContent) {
            return false;
        }

        return 1 === openssl_verify($zipContent, $rawSig, $publicKey, OPENSSL_ALGO_SHA256);
    }

    /**
     * Scan extracted package files for malicious PHP code and dangerous functions before installation.
     */
    private function _scanPackageSecurity(string $tmpPath): bool
    {
        if (! is_dir($tmpPath)) {
            return false;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tmpPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $dangerousPatterns = [
            '/\b(?:eval|passthru|shell_exec|system|proc_open|popen)\s*\(/i',
            '/\bassert\s*\(\s*(?:base64_decode|\$_|\$http_)/i',
            '/\b(?:base64_decode|gzinflate|gzuncompress|str_rot13)\s*\(\s*\$_(?:POST|GET|REQUEST|COOKIE|SERVER)/i',
            '/\$(?:_POST|_GET|_REQUEST|_COOKIE)\s*\[[^\]]+\]\s*\(\s*\$/i'
        ];

        foreach ($files as $file) {
            if ($file->isFile() && 'php' === strtolower($file->getExtension())) {
                $content = file_get_contents($file->getRealPath());

                if (false === $content) {
                    return false;
                }

                foreach ($dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Copy directory recursively
     */
    private function _copyDirectory(string $source, string $destination): bool
    {
        if (! is_dir($source)) {
            return false;
        }

        $dir = opendir($source);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        while (false !== ($file = readdir($dir))) {
            if ('.' !== $file && '..' !== $file) {
                if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                    $this->_copyDirectory($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                } else {
                    copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                }
            }
        }

        closedir($dir);

        return true;
    }

    /**
     * Resolve target version from extracted package files (composer.json).
     */
    private function _getPackageVersion(string $tmpPath): ?string
    {
        $manifestPath = $this->_findFileInDirectory($tmpPath, 'composer.json');

        if ($manifestPath && file_exists($manifestPath)) {
            $packageData = json_decode(file_get_contents($manifestPath));

            if (! empty($packageData->version)) {
                return trim((string) $packageData->version);
            }
        }

        return null;
    }
}
