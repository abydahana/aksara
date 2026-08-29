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

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        $this->setTitle(phrase('Core System Updater'))
        ->setIcon('mdi mdi-update')

        ->setOutput([
            'updater' => $this->pingUpstream(true)
        ])

        ->render();
    }

    /**
     * Download and apply a signed remote update.
     */
    public function update()
    {
        if (! $this->validToken($this->request->getPost('_token'), ['administrative/updater'])) {
            return throw_exception(403, phrase('The security token is invalid or expired.'), current_page('../'));
        }

        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), go_to());
        }

        try {
            $curl = Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false
            ]);

            $response = $curl->post(
                'https://www.aksaracms.com/updater',
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
                return $this->_runUpdater($response);
            }
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage(), current_page('../'));
        }

        return throw_exception(404, phrase('No update are available at the moment.'), current_page('../'));
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
                        ' . phrase('Manual update accepts release.zip from the official GitHub release. The uploaded package must match the signed release manifest.') . '
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">
                            ' . phrase('Signed Release Package (release.zip)') . '
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

        $uploadedZipPath = $_FILES['file']['tmp_name'];

        return $this->_runManualUpdater($uploadedZipPath);
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
     * Run the signed manifest based updater.
     */
    private function _runUpdater(object $response)
    {
        $publicKey = trim((string) get_setting('aksara_public_key'));

        if (
            empty($response->manifest) ||
            empty($response->manifest_signature) ||
            empty($response->package) ||
            empty($publicKey) ||
            ! str_contains($publicKey, 'PUBLIC KEY') ||
            ! $this->_isTrustedRemoteUrl($response->manifest) ||
            ! $this->_isTrustedRemoteUrl($response->manifest_signature) ||
            ! $this->_isTrustedRemoteUrl($response->package)
        ) {
            return throw_exception(400, ['package' => phrase('Update canceled! Package signature or integrity check failed.')]);
        }

        $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'updater_' . sha1($response->manifest . microtime(true));
        $lockPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'updater.lock';
        $manifestPath = $tmpPath . DIRECTORY_SEPARATOR . 'manifest.json';
        $manifestSignaturePath = $tmpPath . DIRECTORY_SEPARATOR . 'manifest.sig';
        $downloadedZip = $tmpPath . DIRECTORY_SEPARATOR . 'release.zip';

        try {
            if (is_file($lockPath) && filemtime($lockPath) > time() - 900) {
                return throw_exception(400, ['package' => phrase('Another update process is currently running. Please try again later.')]);
            }

            file_put_contents($lockPath, (string) getmypid());

            if (! is_dir($tmpPath) && ! mkdir($tmpPath, 0755, true)) {
                throw new \RuntimeException(phrase('Unable to prepare updater temporary directory.'));
            }

            $this->_downloadRemoteFile($response->manifest, $manifestPath);
            $this->_downloadRemoteFile($response->manifest_signature, $manifestSignaturePath);

            $manifestJson = file_get_contents($manifestPath);
            $manifestSignature = file_get_contents($manifestSignaturePath);

            if (! is_string($manifestJson) || ! is_string($manifestSignature) || ! $this->_verifyManifestSignature($manifestJson, $manifestSignature, $publicKey)) {
                throw new \RuntimeException(phrase('Update canceled! Package signature or integrity check failed.'));
            }

            $manifest = json_decode($manifestJson);
            $manifestError = $this->_validateReleaseManifest($manifest, $response);

            if ($manifestError) {
                throw new \RuntimeException($manifestError);
            }

            $versionError = $this->_validateVersionSequence(aksara('version'), $manifest->version);

            if ($versionError) {
                throw new \RuntimeException($versionError);
            }

            $compatibilityError = $this->_validateManifestCompatibility($manifest);

            if ($compatibilityError) {
                throw new \RuntimeException($compatibilityError);
            }

            $this->_downloadRemoteFile($response->package, $downloadedZip);

            if (! $this->_verifyPackageHash($downloadedZip, $manifest)) {
                throw new \RuntimeException(phrase('Update canceled! Package signature or integrity check failed.'));
            }

            return $this->_applyVerifiedPackage($downloadedZip, $manifest, $tmpPath, $lockPath);
        } catch (Throwable $e) {
            $this->_rmdir($tmpPath);
            @unlink($lockPath);

            return throw_exception(400, ['package' => $e->getMessage()]);
        }
    }

    /**
     * Validate an uploaded release.zip against the signed GitHub release manifest.
     */
    private function _runManualUpdater(string $uploadedZipPath)
    {
        $publicKey = trim((string) get_setting('aksara_public_key'));
        $zip = new ZipArchive();
        $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'manual_update_' . sha1($uploadedZipPath . microtime(true));
        $lockPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'updater.lock';
        $manifestPath = $tmpPath . DIRECTORY_SEPARATOR . 'manifest.json';
        $manifestSignaturePath = $tmpPath . DIRECTORY_SEPARATOR . 'manifest.sig';

        if (empty($publicKey) || ! str_contains($publicKey, 'PUBLIC KEY')) {
            return throw_exception(400, ['file' => phrase('Update canceled! Package signature or integrity check failed.')]);
        }

        if ($zip->open($uploadedZipPath) !== true) {
            return throw_exception(400, ['file' => phrase('Unable to open the uploaded zip update package.')]);
        }

        $zipOpen = true;

        try {
            $targetVersion = $this->_getPackageVersionFromZip($zip);
            $zip->close();
            $zipOpen = false;

            if (! $targetVersion) {
                throw new \RuntimeException(phrase('Invalid update package! Version information was missing in composer.json.'));
            }

            $versionError = $this->_validateVersionSequence(aksara('version'), $targetVersion);

            if ($versionError) {
                throw new \RuntimeException($versionError);
            }

            $response = $this->_resolveGithubReleasePayload($targetVersion);

            if (! $response) {
                throw new \RuntimeException(phrase('Update canceled! The uploaded package does not match an official signed GitHub release.'));
            }

            if (is_file($lockPath) && filemtime($lockPath) > time() - 900) {
                return throw_exception(400, ['file' => phrase('Another update process is currently running. Please try again later.')]);
            }

            file_put_contents($lockPath, (string) getmypid());

            if (! is_dir($tmpPath) && ! mkdir($tmpPath, 0755, true)) {
                throw new \RuntimeException(phrase('Unable to prepare updater temporary directory.'));
            }

            $this->_downloadRemoteFile($response->manifest, $manifestPath);
            $this->_downloadRemoteFile($response->manifest_signature, $manifestSignaturePath);

            $manifestJson = file_get_contents($manifestPath);
            $manifestSignature = file_get_contents($manifestSignaturePath);

            if (! is_string($manifestJson) || ! is_string($manifestSignature) || ! $this->_verifyManifestSignature($manifestJson, $manifestSignature, $publicKey)) {
                throw new \RuntimeException(phrase('Update canceled! Package signature or integrity check failed.'));
            }

            $manifest = json_decode($manifestJson);
            $manifestError = $this->_validateReleaseManifest($manifest, $response);

            if ($manifestError) {
                throw new \RuntimeException($manifestError);
            }

            $compatibilityError = $this->_validateManifestCompatibility($manifest);

            if ($compatibilityError) {
                throw new \RuntimeException($compatibilityError);
            }

            if (! $this->_verifyPackageHash($uploadedZipPath, $manifest)) {
                throw new \RuntimeException(phrase('Update canceled! The uploaded package does not match the signed GitHub release manifest.'));
            }

            return $this->_applyVerifiedPackage($uploadedZipPath, $manifest, $tmpPath, $lockPath);
        } catch (Throwable $e) {
            if ($zipOpen) {
                $zip->close();
            }

            $this->_rmdir($tmpPath);
            @unlink($lockPath);

            return throw_exception(400, ['file' => $e->getMessage()]);
        }
    }

    /**
     * Apply a package after its manifest signature and package hash are verified.
     */
    private function _applyVerifiedPackage(string $zipPath, object $manifest, string $tmpPath, string $lockPath)
    {
        $stagingPath = $tmpPath . DIRECTORY_SEPARATOR . 'staging';
        $backupPath = $tmpPath . DIRECTORY_SEPARATOR . '_BACKUP_' . date('Y-m-d_His', time()) . '.zip';
        $oldDependencies = json_decode((string) file_get_contents(ROOTPATH . 'composer.json'), true);
        $dependencyUpdated = [];
        $appliedFiles = [];
        $existingFiles = [];

        try {
            if (! is_dir($stagingPath) && ! mkdir($stagingPath, 0755, true)) {
                throw new \RuntimeException(phrase('Unable to prepare updater temporary directory.'));
            }

            $zip = new ZipArchive();

            if ($zip->open($zipPath) !== true) {
                throw new \RuntimeException(phrase('Unable to open the update package.'));
            }

            if (! $this->_validateZipArchiveAgainstManifest($zip, $manifest) || ! $this->_extractZipArchive($zip, $stagingPath)) {
                $zip->close();

                throw new \RuntimeException(phrase('Unable to extract your update package. Zip entry failed security validation.'));
            }

            $zip->close();

            if (! $this->_scanPackageSecurity($stagingPath)) {
                throw new \RuntimeException(phrase('Update canceled! Malicious or unsafe PHP code was detected in the update package.'));
            }

            $packageFiles = $this->_collectManifestPackageFiles($stagingPath, $manifest);
            $existingFiles = array_values(array_filter($packageFiles, fn ($path) => is_file(ROOTPATH . $path)));

            if (! $this->_createUpdateBackup($backupPath, $manifest)) {
                throw new \RuntimeException(phrase('Update canceled due to inability to write the backup file!'));
            }

            $appliedFiles = $this->_copyManifestFiles($stagingPath, ROOTPATH, $manifest);

            if (! $appliedFiles) {
                throw new \RuntimeException(phrase('Update failed due to inability to write the updater file.'));
            }

            $newDependencies = json_decode((string) file_get_contents(ROOTPATH . 'composer.json'), true);

            if (isset($oldDependencies['require']) && isset($newDependencies['require']) && $oldDependencies['require'] !== $newDependencies['require']) {
                $dependencyUpdated = array_diff_assoc($newDependencies['require'], $oldDependencies['require']);
                $newDependencies['require'] = array_replace($oldDependencies['require'], $newDependencies['require']);
                file_put_contents(ROOTPATH . 'composer.json', json_encode($newDependencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }

            try {
                $runMigrations = $manifest->apply->run_migrations ?? true;
                $runSeeders = $manifest->apply->run_seeders ?? true;

                if ($runMigrations) {
                    $migration = Services::migrations()->setNamespace('Aksara');
                    $migration->latest();
                }

                if ($runSeeders) {
                    $this->_runSeeds();
                }
            } catch (Throwable $e) {
                $this->_restoreUpdateBackup($backupPath, $appliedFiles, $existingFiles);
                $appliedFiles = [];

                throw $e;
            }

            $this->_rmdir($tmpPath);
            @unlink($lockPath);

            return $this->_updateSuccessResponse($dependencyUpdated);
        } catch (Throwable $e) {
            if ($appliedFiles && is_file($backupPath)) {
                try {
                    $this->_restoreUpdateBackup($backupPath, $appliedFiles, $existingFiles);
                } catch (Throwable $restoreError) {
                    throw new \RuntimeException($e->getMessage() . ' ' . phrase('Backup restore failed:') . ' ' . $restoreError->getMessage());
                }
            }

            throw $e;
        }
    }

    /**
     * Resolve signed release metadata from GitHub for a manually uploaded package.
     */
    private function _resolveGithubReleasePayload(string $version): ?object
    {
        $release = $this->_fetchGithubReleaseByTag($version) ?: $this->_fetchGithubReleaseByTag('v' . $version);

        if (! $release || empty($release->tag_name)) {
            return null;
        }

        $releaseVersion = $this->_normalizeVersion($release->tag_name);

        if (! $releaseVersion || ! hash_equals($version, $releaseVersion)) {
            return null;
        }

        $assets = $this->_collectGithubReleaseAssets($release);

        if (empty($assets['package']) || empty($assets['manifest']) || empty($assets['manifest_signature'])) {
            return null;
        }

        foreach ($assets as $url) {
            if (! $this->_isTrustedRemoteUrl($url)) {
                return null;
            }
        }

        return (object) [
            'version' => $releaseVersion,
            'manifest' => $assets['manifest'],
            'manifest_signature' => $assets['manifest_signature'],
            'package' => $assets['package']
        ];
    }

    private function _fetchGithubReleaseByTag(string $tag): ?object
    {
        $curl = Services::curlrequest([
            'timeout' => 10,
            'http_errors' => false
        ]);

        $response = $curl->get(
            'https://api.github.com/repos/abydahana/aksara/releases/tags/' . rawurlencode($tag),
            [
                'allow_redirects' => [
                    'max' => 2
                ],
                'headers' => [
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => $this->_userAgent()
                ]
            ]
        );

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $release = json_decode($response->getBody());

        if (! is_object($release) || ! empty($release->draft) || ! empty($release->prerelease)) {
            return null;
        }

        return $release;
    }

    private function _collectGithubReleaseAssets(object $release): array
    {
        $assets = [
            'package' => null,
            'manifest' => null,
            'manifest_signature' => null
        ];

        if (empty($release->assets) || ! is_array($release->assets)) {
            return $assets;
        }

        foreach ($release->assets as $asset) {
            if (empty($asset->name) || empty($asset->browser_download_url)) {
                continue;
            }

            if ('release.zip' === $asset->name) {
                $assets['package'] = $asset->browser_download_url;
            } elseif ('manifest.json' === $asset->name) {
                $assets['manifest'] = $asset->browser_download_url;
            } elseif ('manifest.sig' === $asset->name) {
                $assets['manifest_signature'] = $asset->browser_download_url;
            }
        }

        return $assets;
    }

    private function _getPackageVersionFromZip(ZipArchive $zip): ?string
    {
        for ($i = 0, $count = $zip->numFiles; $i < $count; $i++) {
            $entryName = $zip->getNameIndex($i);
            $entryPath = $this->_normalizeRelativePath($entryName);

            if (false === $entryName || ! $entryPath || ! $this->_isSafeZipEntry($entryName)) {
                return null;
            }

            if ('composer.json' !== $entryPath) {
                continue;
            }

            $composerJson = $zip->getFromIndex($i);

            if (! is_string($composerJson)) {
                return null;
            }

            $composer = json_decode($composerJson);

            if (! empty($composer->version)) {
                return $this->_normalizeVersion($composer->version);
            }
        }

        return null;
    }

    private function _normalizeVersion(mixed $version): ?string
    {
        if (! is_scalar($version)) {
            return null;
        }

        $version = ltrim(trim((string) $version), 'vV');

        if (! preg_match('/^\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', $version)) {
            return null;
        }

        return $version;
    }

    private function _userAgent(): string
    {
        return service('request')->getUserAgent()->getAgentString() ?: 'Aksara Updater';
    }

    private function _updateSuccessResponse(array $dependencyUpdated)
    {
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
    }

    /**
     * Download a trusted remote artifact to disk.
     */
    private function _downloadRemoteFile(string $url, string $destination): void
    {
        if (! $this->_isTrustedRemoteUrl($url)) {
            throw new \RuntimeException(phrase('Update canceled! Package signature or integrity check failed.'));
        }

        $curl = Services::curlrequest([
            'timeout' => 30,
            'http_errors' => false
        ]);

        $response = $curl->get($url, [
            'allow_redirects' => [
                'max' => 2
            ],
            'headers' => [
                'Referer' => base_url(),
                'User-Agent' => $this->_userAgent()
            ]
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new \RuntimeException(phrase('Unable to download the update package.'));
        }

        $body = $response->getBody();

        if ('' === $body || false === file_put_contents($destination, $body)) {
            throw new \RuntimeException(phrase('Unable to download the update package.'));
        }
    }

    /**
     * Keep updater downloads on official and release asset hosts only.
     */
    private function _isTrustedRemoteUrl(?string $url): bool
    {
        $parts = parse_url((string) $url);
        $host = strtolower($parts['host'] ?? '');

        if ('https' !== ($parts['scheme'] ?? '') || ! $host) {
            return false;
        }

        $allowedHosts = [
            'aksaracms.com',
            'www.aksaracms.com',
            'github.com',
            'objects.githubusercontent.com',
            'github-releases.githubusercontent.com',
            'raw.githubusercontent.com'
        ];

        return in_array($host, $allowedHosts, true);
    }

    /**
     * Verify detached base64 signature for manifest.json.
     */
    private function _verifyManifestSignature(string $manifestJson, string $manifestSignature, string $publicKey): bool
    {
        $signature = base64_decode(trim($manifestSignature), true);

        if (false === $signature) {
            return false;
        }

        return 1 === openssl_verify($manifestJson, $signature, $publicKey, OPENSSL_ALGO_SHA256);
    }

    /**
     * Validate signed release manifest metadata.
     */
    private function _validateReleaseManifest(mixed $manifest, object $response): ?string
    {
        if (
            ! is_object($manifest) ||
            1 !== ($manifest->schema ?? null) ||
            'aksara' !== ($manifest->product ?? null) ||
            empty($manifest->version) ||
            ! preg_match('/^\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', $manifest->version) ||
            'release.zip' !== ($manifest->package->name ?? null) ||
            empty($manifest->package->sha256) ||
            ! preg_match('/^[a-f0-9]{64}$/i', $manifest->package->sha256) ||
            empty($manifest->apply->allowed_paths) ||
            ! is_array($manifest->apply->allowed_paths)
        ) {
            return phrase('Update canceled! Release manifest is invalid.');
        }

        if (! empty($response->version) && ! hash_equals((string) $response->version, (string) $manifest->version)) {
            return phrase('Update canceled! Release manifest is invalid.');
        }

        if (! empty($manifest->min_version) && version_compare(aksara('version'), $manifest->min_version, '<')) {
            return phrase('Version step too large! You are currently on {{ current }}, so you must update to {{ expected }} first before updating to {{ target }}.', [
                'current' => aksara('version'),
                'expected' => $manifest->min_version,
                'target' => $manifest->version
            ]);
        }

        if (! empty($manifest->max_version) && version_compare(aksara('version'), $manifest->max_version, '>')) {
            return phrase('The selected update package is not compatible with your current system version.');
        }

        return null;
    }

    /**
     * Check runtime compatibility declared by the signed manifest.
     */
    private function _validateManifestCompatibility(object $manifest): ?string
    {
        $phpRequirement = $manifest->compatibility->php ?? null;
        $ciRequirement = $manifest->compatibility->codeigniter ?? null;

        if ($phpRequirement && ! $this->_meetsMinimumRequirement(PHP_VERSION, $phpRequirement)) {
            return phrase('The selected update package is not compatible with your current PHP version.');
        }

        if ($ciRequirement && ! $this->_meetsMinimumRequirement(\CodeIgniter\CodeIgniter::CI_VERSION, $ciRequirement)) {
            return phrase('The selected update package is not compatible with your current CodeIgniter version.');
        }

        return null;
    }

    /**
     * Check a composer-style lower-bound requirement.
     */
    private function _meetsMinimumRequirement(string $version, string $constraint): bool
    {
        if (! preg_match('/\d+(?:\.\d+){0,2}/', $constraint, $matches)) {
            return true;
        }

        $minimum = $matches[0];
        $parts = explode('.', $minimum);

        while (count($parts) < 3) {
            $parts[] = '0';
        }

        return version_compare($version, implode('.', $parts), '>=');
    }

    /**
     * Verify release.zip against the signed manifest.
     */
    private function _verifyPackageHash(string $zipPath, object $manifest): bool
    {
        if (! is_file($zipPath)) {
            return false;
        }

        $fileSize = filesize($zipPath);

        if (false === $fileSize) {
            return false;
        }

        if (isset($manifest->package->size) && (! is_numeric($manifest->package->size) || (int) $manifest->package->size !== $fileSize)) {
            return false;
        }

        $packageHash = hash_file('sha256', $zipPath);

        if (! is_string($packageHash)) {
            return false;
        }

        return hash_equals(strtolower($manifest->package->sha256), strtolower($packageHash));
    }

    /**
     * Validate every zip entry against the signed manifest allowlist.
     */
    private function _validateZipArchiveAgainstManifest(ZipArchive $zip, object $manifest): bool
    {
        for ($i = 0, $count = $zip->numFiles; $i < $count; $i++) {
            $entryName = $zip->getNameIndex($i);
            $entryPath = $this->_normalizeRelativePath($entryName);

            if (false === $entryName || ! $entryPath || ! $this->_isSafeZipEntry($entryName) || ! $this->_isManifestPathAllowed($entryPath, $manifest)) {
                return false;
            }

            $opsys = 0;
            $attributes = 0;

            if (
                method_exists($zip, 'getExternalAttributesIndex') &&
                $zip->getExternalAttributesIndex($i, $opsys, $attributes) &&
                0120000 === (($attributes >> 16) & 0170000)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Collect allowed package files from staging.
     */
    private function _collectManifestPackageFiles(string $source, object $manifest): array
    {
        $files = [];
        $source = rtrim($source, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $relativePath = $this->_normalizeRelativePath(substr($file->getRealPath(), strlen($source)));

            if ($relativePath && $this->_isManifestPathAllowed($relativePath, $manifest)) {
                $files[] = $relativePath;
            }
        }

        return $files;
    }

    /**
     * Create backup for paths the signed manifest may replace.
     */
    private function _createUpdateBackup(string $backupPath, object $manifest): bool
    {
        $zip = new ZipArchive();
        $added = [];

        if (true !== $zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            return false;
        }

        foreach ($this->_manifestAllowedPaths($manifest) as $path) {
            $absolutePath = ROOTPATH . rtrim($path, '/');

            if (str_ends_with($path, '/')) {
                if (! is_dir($absolutePath)) {
                    continue;
                }

                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absolutePath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
                    if ($file->isDir()) {
                        continue;
                    }

                    $relativePath = $this->_normalizeRelativePath(str_replace(ROOTPATH, '', $file->getRealPath()));

                    if ($relativePath && $this->_isManifestPathAllowed($relativePath, $manifest) && ! isset($added[$relativePath])) {
                        $zip->addFile($file->getRealPath(), $relativePath);
                        $added[$relativePath] = true;
                    }
                }
            } elseif (is_file($absolutePath) && ! isset($added[$path])) {
                $zip->addFile($absolutePath, $path);
                $added[$path] = true;
            }
        }

        return $zip->close();
    }

    /**
     * Copy staged update files to the installation root.
     */
    private function _copyManifestFiles(string $source, string $destination, object $manifest): array
    {
        $copied = [];
        $source = rtrim($source, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $destination = rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $relativePath = $this->_normalizeRelativePath(substr($file->getRealPath(), strlen($source)));

            if (! $relativePath || ! $this->_isManifestPathAllowed($relativePath, $manifest)) {
                throw new \RuntimeException(phrase('Unable to extract your update package. Zip entry failed security validation.'));
            }

            $targetPath = $destination . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $targetDirectory = dirname($targetPath);

            if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0755, true)) {
                throw new \RuntimeException(phrase('Update failed due to inability to write the updater file.'));
            }

            if (! copy($file->getRealPath(), $targetPath)) {
                throw new \RuntimeException(phrase('Update failed due to inability to write the updater file.'));
            }

            $copied[] = $relativePath;
        }

        return $copied;
    }

    /**
     * Restore replaced files and remove files created by a failed update.
     */
    private function _restoreUpdateBackup(string $backupPath, array $appliedFiles, array $existingFiles): void
    {
        $existingMap = array_flip($existingFiles);

        foreach ($appliedFiles as $relativePath) {
            if (! isset($existingMap[$relativePath]) && is_file(ROOTPATH . $relativePath)) {
                unlink(ROOTPATH . $relativePath);
            }
        }

        $zip = new ZipArchive();

        if ($zip->open($backupPath) !== true || ! $this->_extractZipArchive($zip, ROOTPATH)) {
            $zip->close();

            throw new \RuntimeException(phrase('Unable to restore update backup.'));
        }

        $zip->close();
    }

    /**
     * Resolve allowlist from the signed manifest.
     */
    private function _manifestAllowedPaths(object $manifest): array
    {
        return $this->_normalizeManifestRules($manifest->apply->allowed_paths ?? []);
    }

    /**
     * Resolve blocked paths from the signed manifest plus local hard blocks.
     */
    private function _manifestBlockedPaths(object $manifest): array
    {
        return array_values(array_unique(array_merge(
            [
                '.env',
                '.git/',
                'config.php',
                'modules/',
                'node_modules/',
                'package-lock.json',
                'public/uploads/',
                'vendor/',
                'writable/'
            ],
            $this->_normalizeManifestRules($manifest->apply->blocked_paths ?? [])
        )));
    }

    /**
     * Normalize path rules while preserving directory rule suffix.
     */
    private function _normalizeManifestRules(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $rule) {
            $rule = str_replace('\\', '/', trim((string) $rule));
            $isDirectoryRule = str_ends_with($rule, '/');
            $rule = $this->_normalizeRelativePath($rule);

            if ($rule) {
                $normalized[] = $rule . ($isDirectoryRule ? '/' : '');
            }
        }

        return $normalized;
    }

    /**
     * Check whether a relative path is allowed and not blocked.
     */
    private function _isManifestPathAllowed(string $path, object $manifest): bool
    {
        $path = $this->_normalizeRelativePath($path);

        if (! $path) {
            return false;
        }

        foreach ($this->_manifestBlockedPaths($manifest) as $rule) {
            if ($this->_pathMatchesRule($path, $rule)) {
                return false;
            }
        }

        foreach ($this->_manifestAllowedPaths($manifest) as $rule) {
            if ($this->_pathMatchesRule($path, $rule)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check a file path against a manifest rule.
     */
    private function _pathMatchesRule(string $path, string $rule): bool
    {
        if (str_ends_with($rule, '/')) {
            $directory = rtrim($rule, '/');

            return $path === $directory || str_starts_with($path, $rule);
        }

        return $path === $rule;
    }

    /**
     * Normalize relative paths used by updater packages.
     */
    private function _normalizeRelativePath(?string $path): ?string
    {
        $path = str_replace('\\', '/', trim((string) $path));
        $path = preg_replace('#/+#', '/', $path);
        $path = ltrim($path, '/');
        $path = preg_replace('#^\./#', '', $path);
        $path = rtrim($path, '/');

        if (! $path || str_contains($path, "\0") || preg_match('/(?:^|\/)\.\.(?:\/|$)/', $path)) {
            return null;
        }

        return $path;
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
}
