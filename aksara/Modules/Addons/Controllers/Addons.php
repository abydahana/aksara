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

namespace Aksara\Modules\Addons\Controllers;

use Throwable;
use ZipArchive;
use Config\Services;
use Aksara\Laboratory\Core;

class Addons extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        helper('filesystem');
    }

    public function index()
    {
        if ('market' == $this->request->getPost('source')) {
            return $this->_listing();
        }

        $this->setTitle(phrase('Add-Ons Market'))
        ->setIcon('mdi mdi-cart')

        ->render();
    }

    /**
     * Grab add-ons detail from Aksara Market
     */
    public function detail()
    {
        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return throw_exception(403, phrase('The cURL module is not enabled.'), current_page('../'));
        }

        $package = [];
        try {
            $curl = Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false
            ]);

            $response = $curl->post(
                'https://www.aksaracms.com/market/api/detail',
                [
                    'allow_redirects' => [
                        'max' => 2
                    ],
                    'headers' => [
                        'Referer' => base_url()
                    ],
                    'form_params' => [
                        'type' => $this->request->getGet('type'),
                        'initial' => $this->request->getGet('item'),
                        'version' => aksara('version')
                    ]
                ]
            );
        } catch (Throwable $e) {
            return make_json([
                'error' => $e->getMessage()
            ]);
        }

        $package = json_decode($response->getBody());

        //dd($package);

        if ($response->getStatusCode() !== 200) {
            return throw_exception(403, $response->getReasonPhrase(), current_page('../'));
        }

        $this->setTitle((isset($package->name) ? $package->name : phrase('No item found!')))
        ->setIcon('mdi ' . ($this->request->getGet('type') == 'theme' ? 'mdi-palette' : 'mdi-puzzle'))
        ->setOutput([
            'detail' => $package
        ])

        ->render(null, 'detail');
    }

    /**
     * Install add-ons
     */
    public function install()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null, 'type' => null]));
        }

        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return throw_exception(403, phrase('The cURL module is not enabled.'), go_to());
        }

        if (in_array($this->request->getGet('type'), ['theme', 'module'])) {
            try {
                $curl = Services::curlrequest([
                    'timeout' => 5,
                    'http_errors' => false
                ]);

                $response = $curl->post(
                    'https://www.aksaracms.com/market/api/detail',
                    [
                        'allow_redirects' => [
                            'max' => 2
                        ],
                        'headers' => [
                            'Referer' => base_url()
                        ],
                        'form_params' => [
                            'type' => $this->request->getGet('type'),
                            'initial' => $this->request->getGet('item'),
                            'version' => aksara('version'),
                            'install' => true
                        ]
                    ]
                );
            } catch (Throwable $e) {
                return make_json([
                    'error' => $e->getMessage()
                ]);
            }

            $package = json_decode($response->getBody());

            if ($response->getStatusCode() !== 200) {
                return throw_exception(403, $response->getReasonPhrase(), go_to());
            }

            if ('theme' == $this->request->getGet('type')) {
                $type = 'theme';
                $path = 'themes';
            } else {
                $type = 'module';
                $path = 'modules';
            }

            if ($package) {
                // Early Pre-check: Validate addon metadata and Public Key BEFORE downloading
                if (empty($package->repository) || (empty($package->sha256) && empty($package->signature))) {
                    return throw_exception(400, ['file' => phrase('Add-on installation canceled! Package signature or integrity check failed.')]);
                }

                $publicKey = trim((string) get_setting('aksara_public_key'));

                if (! empty($package->signature) && (empty($publicKey) || ! str_contains($publicKey, 'PUBLIC KEY'))) {
                    return throw_exception(400, ['file' => phrase('Add-on installation canceled! Package signature or integrity check failed.')]);
                }

                // Get update package from remote server
                $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->request->getGet('item');

                // Check if temporary path is available
                if (! is_dir($tmpPath)) {
                    try {
                        // Try create temporary path
                        mkdir($tmpPath, 0755, true);

                        // Copy the repository to temporary path
                        copy($package->repository, $tmpPath . DIRECTORY_SEPARATOR . 'file.zip');

                        // Verify digital signature and SHA-256 integrity of the addon package before extraction
                        if (! $this->_verifyPackageSignature($tmpPath . DIRECTORY_SEPARATOR . 'file.zip', $package)) {
                            $this->_rmdir($tmpPath);

                            return throw_exception(400, ['file' => phrase('Add-on installation canceled! Package signature or integrity check failed.')]);
                        }
                    } catch (Throwable $e) {
                        // Action error, throw exception
                        return throw_exception(403, $response->getReasonPhrase(), go_to());
                    }
                }

                // Load the zip class
                $zip = new ZipArchive();

                // Unzip the repository
                $unzip = $zip->open($tmpPath . DIRECTORY_SEPARATOR . 'file.zip');

                if (true === $unzip) {
                    // Validate the zip contents and extract safely
                    if (! $this->_extractZipArchive($zip, $tmpPath)) {
                        // Close opened zip
                        $zip->close();

                        // Remove temporary directory
                        $this->_rmdir($tmpPath);

                        return throw_exception(400, ['file' => phrase('Unable to extract the selected ' . $type . ' package.')]);
                    }

                    $files = directory_map($tmpPath);

                    if (! $files) {
                        // Close opened zip
                        $zip->close();

                        return throw_exception(400, ['file' => phrase('Unable to extract the selected ' . $type . ' package.')]);
                    }

                    $validPackage = false;
                    $packagePath = null;
                    $extract = false;

                    foreach ($files as $key => $val) {
                        if (! $packagePath && ! in_array($key, ['__MACOSX' . DIRECTORY_SEPARATOR])) {
                            $packagePath = basename(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, trim($key, '/\\')));
                        }

                        if (! is_array($val)) {
                            continue;
                        }

                        foreach ($val as $_key => $_val) {
                            if ('theme.json' == $_val && file_exists($tmpPath . DIRECTORY_SEPARATOR . $key . $_val)) {
                                $package = json_decode(file_get_contents($tmpPath . DIRECTORY_SEPARATOR . $key . $_val));

                                if (
                                    ! $package ||
                                    ! isset($package->name) ||
                                    ! isset($package->description) ||
                                    ! isset($package->version) ||
                                    ! isset($package->author) ||
                                    ! isset($package->compatibility) ||
                                    ! isset($package->type) ||
                                    ! in_array($package->type, ['module', 'backend', 'frontend'])
                                ) {
                                    // Close opened zip
                                    $zip->close();

                                    // Remove temporary directory
                                    $this->_rmdir($tmpPath);

                                    return throw_exception(403, phrase('The package manifest was invalid.'));
                                } elseif (! in_array(aksara('version'), $package->compatibility)) {
                                    // Close opened zip
                                    $zip->close();

                                    // Remove temporary directory
                                    $this->_rmdir($tmpPath);

                                    return throw_exception(403, phrase('The {{type}} package is not compatible with your current Aksara version.', ['type' => $type]));
                                }

                                $validPackage = true;
                            }
                        }
                    }

                    if (! $validPackage) {
                        // Close opened zip
                        $zip->close();

                        // Remove temporary directory
                        $this->_rmdir($tmpPath);

                        return throw_exception(403, phrase('No package manifest found on your module package.'));
                    }

                    // Check if the directory already exists
                    if (is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $packagePath) && $this->request->getPost('upgrade') != $this->request->getGet('item')) {
                        // Close opened zip
                        $zip->close();

                        // Remove temporary directory
                        $this->_rmdir($tmpPath);

                        // Offer upgrade version of selected module or theme
                        $html = '
                            <form action="' . current_page() . '" method="POST" class="--validate-form">
                                <div class="text-center">
                                    ' . phrase('The {{type}} package with same structure is already installed.', ['type' => $type]) . ' ' . phrase('Do you want to upgrade the {{type}} instead?', ['type' => $type]) . '
                                </div>
                                <hr class="mx--3 border-secondary" />
                                <input type="hidden" name="upgrade" value="' . $this->request->getGet('item') . '" />
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
                                            <button type="submit" class="btn btn-danger">
                                                <i class="mdi mdi-check"></i>
                                                ' . phrase('Continue') . '
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        ';

                        return make_json([
                            'status' => 200,
                            'meta' => [
                                'title' => phrase('Action Warning'),
                                'icon' => 'mdi mdi-alert-outline',
                                'popup' => true
                            ],
                            'content' => $html
                        ]);
                    }

                    if (is_writable(ROOTPATH . $path)) {
                        // Extract package contents safely
                        $extract = $this->_extractZipArchive($zip, ROOTPATH . $path);

                        // Close zip
                        $zip->close();
                    }

                    if ($extract && is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $packagePath)) {
                        try {
                            $autoloader = Services::autoloader();

                            // Push module namespace to filelocator
                            $autoloader->addNamespace('Modules\\' . $packagePath, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $packagePath);

                            // Run install migration
                            $migration = Services::migrations()->setNameSpace('Modules\\' . $packagePath);

                            // Trying to run the migration
                            if ($migration->latest()) {
                                //
                            }
                        } catch (Throwable $e) {
                            // Migration error, delete module
                            $this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $packagePath);

                            return throw_exception(400, ['file' => $e->getMessage()]);
                        }

                        // Run additional command to assign the menus or permissions
                        if (isset($package->menu) && is_array($package->menu)) {
                            // Assign the available menus
                            foreach ($package->menu as $key => $val) {
                                // Check if theme property contain valid menu
                                if (
                                    ! isset($val->placement) ||
                                    ! in_array($val->placement, ['header', 'sidebar']) ||
                                    ! isset($val->group) ||
                                    ! isset($val->link) ||
                                    ! is_array($val->link) ||
                                    ! $val->link
                                ) {
                                    continue;
                                }

                                // Check if given group is on valid array
                                if (! is_array($val->group)) {
                                    // Otherwise, convert group as array
                                    $val->group = [$val->group];
                                }

                                // Populate given links as array with adding the unique id
                                $links = str_replace('"label":"', '"id":"' . sha1($packagePath) . '","label":"', json_encode($val->link));
                                $links = json_decode($links, true);

                                // Check if links is available or continue
                                if (! $links) {
                                    continue;
                                }

                                // Loops the given group
                                foreach ($val->group as $_key => $_val) {
                                    // Get the existing menu from the database
                                    $existing = $this->model->getWhere(
                                        'app_menus',
                                        [
                                            'menu_placement' => $val->placement,
                                            'group_id' => $_val
                                        ],
                                        1
                                    )
                                    ->row();

                                    // Populate the link obtained
                                    $serialized = (isset($existing->menu_structure) ? $existing->menu_structure : '[]');
                                    $serialized = ($serialized ? json_decode($serialized, true) : []);

                                    // Check if obtained links is populated
                                    if ($serialized) {
                                        // Make links unique
                                        $serialized = $this->_arrayUnique($serialized, 'slug', $packagePath);

                                        // Merge the old link with new one
                                        $links = array_merge($serialized, $links);
                                    }

                                    if ($existing) {
                                        // Update the menu to the database
                                        $this->model->update(
                                            'app_menus',
                                            [
                                                'menu_structure' => json_encode($links)
                                            ],
                                            [
                                                'menu_id' => $existing->menu_id
                                            ]
                                        );
                                    } else {
                                        // Insert the menu to the database
                                        $this->model->insert(
                                            'app_menus',
                                            [
                                                'menu_placement' => $val->placement,
                                                'menu_label' => phrase('Generated Menu'),
                                                'menu_description' => phrase('Generated menu from module installation.'),
                                                'menu_structure' => json_encode($links),
                                                'group_id' => $_val,
                                                'status' => 1
                                            ]
                                        );
                                    }
                                }
                            }
                        }

                        if (isset($package->permission) && is_object($package->permission)) {
                            foreach ($package->permission as $key => $val) {
                                // Check the value is in correct format or continue
                                if (! is_object($val)) {
                                    continue;
                                }

                                // Loops the given permission
                                foreach ($val as $_key => $_val) {
                                    // Get the privileges from the database
                                    $privileges = $this->model->getWhere(
                                        'app_groups_privileges',
                                        [
                                            'path' => $_key
                                        ],
                                        1
                                    )
                                    ->row('privileges');

                                    // Check if query has results
                                    if ($privileges) {
                                        // Update the existing privileges
                                        $this->model->update(
                                            'app_groups_privileges',
                                            [
                                                'privileges' => json_encode(array_unique(array_merge(json_decode($privileges, true), json_decode(json_encode($_val), true)))),
                                                'last_generated' => date('Y-m-d H:i:s')
                                            ],
                                            [
                                                'path' => $_key
                                            ]
                                        );
                                    } else {
                                        // Otherwise, insert a new one
                                        $this->model->insert(
                                            'app_groups_privileges',
                                            [
                                                'path' => $_key,
                                                'privileges' => json_encode(array_unique($_val)),
                                                'last_generated' => date('Y-m-d H:i:s')
                                            ]
                                        );
                                    }
                                }

                                // Get the existing group privileges
                                $groupPrivileges = $this->model->getWhere(
                                    'app_groups',
                                    [
                                        'group_id' => $key
                                    ],
                                    1
                                )
                                ->row('group_privileges');

                                // Check if group privileges has result
                                if ($groupPrivileges) {
                                    // Update the group privileges obtained
                                    $this->model->update(
                                        'app_groups',
                                        [
                                            'group_privileges' => json_encode(array_merge(json_decode($groupPrivileges, true), json_decode(json_encode($val), true)))
                                        ],
                                        [
                                            'group_id' => $key
                                        ]
                                    );
                                }
                            }
                        }

                        // Remove temporary directory
                        $this->_rmdir($tmpPath);

                        return throw_exception(301, phrase('The selected {{type}} package was successfully installed.', ['type' => $type]), current_page('../' . $type, ['item' => null, 'type' => null]));
                    } else {
                        return throw_exception(403, phrase('Your {{type}} folder seems not writable.', ['type' => $type]));
                    }
                }

                // Remove temporary directory
                $this->_rmdir($tmpPath);

                return throw_exception(403, phrase('Unable to install the selected {{type}}.', ['type' => $type]));
            }

            return throw_exception(404, phrase('The selected {{type}} you would to install is not available.', ['type' => $type]));
        }

        return throw_exception(404, phrase('Your web server needs to be connected to the internet to install the add-ons.'));
    }

    /**
     * Grab add-ons from Aksara Market
     */
    private function _listing()
    {
        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return make_json([
                'error' => phrase('The cURL module is not enabled.')
            ]);
        }

        $themes = directory_map(ROOTPATH . 'themes', 1);
        $installedThemes = [];

        if ($themes) {
            foreach ($themes as $key => $val) {
                if (strpos($val, DIRECTORY_SEPARATOR) !== false) {
                    $installedThemes[] = str_replace(DIRECTORY_SEPARATOR, '', $val);
                }
            }
        }

        $modules = directory_map(ROOTPATH . 'modules', 1);
        $installedModules = [];

        if ($modules) {
            foreach ($modules as $key => $val) {
                if (strpos($val, DIRECTORY_SEPARATOR) !== false) {
                    $installedModules[] = str_replace(DIRECTORY_SEPARATOR, '', $val);
                }
            }
        }

        try {
            $curl = Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false
            ]);

            $response = $curl->post(
                'https://www.aksaracms.com/market/api',
                [
                    'allow_redirects' => [
                        'max' => 2
                    ],
                    'headers' => [
                        'Referer' => base_url()
                    ],
                    'form_params' => [
                        'version' => aksara('version'),
                        'order' => $this->request->getPost('order'),
                        'keyword' => $this->request->getPost('keyword'),
                        'addon_type' => $this->request->getPost('addon_type'),
                        'installed' => json_encode([
                            'themes' => $installedThemes,
                            'modules' => $installedModules
                        ])
                    ]
                ]
            );
        } catch (Throwable $e) {
            return make_json([
                'error' => $e->getMessage()
            ]);
        }

        $package = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            return make_json([
                'error' => $response->getReasonPhrase()
            ]);
        }

        if ($package) {
            foreach ($package as $key => $val) {
                $package[$key]['detail_url'] = current_page('detail', ['item' => $val['path'], 'type' => $val['addon_type']]);
                $package[$key]['install_url'] = current_page('install', ['item' => $val['path'], 'type' => $val['addon_type']]);
            }
        }

        return make_json($package);
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
     * Make array unique by value
     * @param null|mixed $key
     * @param null|mixed $value
     */
    private function _arrayUnique($array = [], $key = null, $value = null)
    {
        $value = strtolower($value);

        foreach ($array as $subKey => $subArray) {
            if (isset($subArray[$key]) && $subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }

        return $array;
    }

    /**
     * Verify SHA-256 hash and RSA public key cryptographic signature of addon package.
     */
    private function _verifyPackageSignature(string $zipPath, object $package): bool
    {
        if (! is_file($zipPath)) {
            return false;
        }

        // 1. Verify SHA-256 hash if provided
        if (! empty($package->sha256)) {
            $fileHash = hash_file('sha256', $zipPath);

            if (! hash_equals(strtolower($package->sha256), strtolower($fileHash))) {
                return false;
            }
        }

        // 2. Verify Cryptographic OpenSSL Signature if signature is provided
        if (! empty($package->signature)) {
            $signature = base64_decode($package->signature, true);
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
        return ! empty($package->sha256) || ! empty($package->signature);
    }
}
