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

namespace Aksara\Modules\Addons\Controllers;

use Config\Services;
use Aksara\Laboratory\Core;
use Throwable;
use ZipArchive;

class Themes extends Core
{
    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        helper('filesystem');

        $this->_primary = $this->request->getGet('item');
    }

    public function index()
    {
        $this->setTitle(phrase('Theme Manager'))
        ->setIcon('mdi mdi-palette')
        ->setOutput([
            'installed' => $this->_installed()
        ])

        ->render();
    }

    /**
     * Get theme detail
     */
    public function detail()
    {
        $package = [];

        try {
            $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json'));
        } catch (Throwable $e) {
            // Safe abstraction
        }

        if ($package) {
            $package->folder = $this->_primary;
            $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $this->setTitle(phrase('Theme Detail'))
        ->setIcon('mdi mdi-palette')
        ->setOutput([
            'detail' => $package
        ])
        ->modalSize('modal-xl')

        ->render();
    }

    /**
     * Update module
     */
    public function update()
    {
        if (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json')) {
            return throw_exception(404, phrase('The package manifest for the selected theme is missing.'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json'));

        if (! isset($package->hash) || ! isset($package->version)) {
            return throw_exception(404, phrase('The selected theme is not supported by the official market.'), current_page('../', ['item' => null]));
        }

        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return throw_exception(403, phrase('The cURL module is not enabled.'), current_page('../', ['item' => null]));
        } elseif (! @fsockopen('www.aksaracms.com', 443)) {
            return throw_exception(403, phrase('Unable to connect to the Aksara Market.'), current_page('../', ['item' => null]));
        }

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
                        'type' => 'theme',
                        'initial' => $package->hash,
                        'version' => aksara('version')
                    ]
                ]
            );
        } catch (Throwable $e) {
            log_message('error', $e->getMessage());
        }

        $upstream = json_decode($response->getBody());

        if ($response->getStatusCode() !== 200) {
            return throw_exception(403, $response->getReasonPhrase(), current_page('../', ['item' => null]));
        } elseif (isset($upstream->version) && $upstream->version > $package->version) {
            $html = '
                <form action="' . current_page('../../../addons/install', ['item' => $upstream->path, 'type' => 'theme']) . '" method="POST" class="--validate-form">
                    <div class="text-center">
                        ' . phrase('A new version of the selected theme is available.') . '
                        <br />
                        <h4>
                            ' . $upstream->name . '
                        </h4>
                        <h5>
                            '. phrase('Version') . ' ' . $upstream->version . '
                        </h5>
                    </div>
                    <hr class="mx--3 border-secondary" />
                    <input type="hidden" name="upgrade" value="' . $upstream->path . '" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-light">
                                    <i class="mdi mdi-window-close"></i>
                                    ' . phrase('Cancel') . '
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="mdi mdi-check"></i>
                                    ' . phrase('Update') . '
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'title' => phrase('Update Available'),
                    'icon' => 'mdi mdi-auto-fix',
                    'popup' => true
                ],
                'content' => $html
            ]);
        }

        return throw_exception(404, phrase('No update available at the moment.'), current_page('../', ['item' => null]));
    }

    /**
     * Activate theme
     */
    public function activate()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null]));
        }

        $this->permission->must_ajax(current_page('../', ['item' => null]));

        if (! $this->request->getPost('theme')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="--validate-form">
                    <div class="text-center">
                        ' . phrase('Are you sure want to activate this theme?') . '
                    </div>
                    <hr class="mx--3 border-secondary" />
                    <input type="hidden" name="theme" value="' . $this->_primary . '" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-light">
                                    <i class="mdi mdi-window-close"></i>
                                    ' . phrase('Cancel') . '
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check"></i>
                                    ' . phrase('Activate') . '
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

        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null]));
        } elseif (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme') . DIRECTORY_SEPARATOR . 'theme.json')) {
            return throw_exception(404, phrase('No theme package manifest were found.'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme') . DIRECTORY_SEPARATOR . 'theme.json'));

        if (! $package || ! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'], true)) {
            return throw_exception(403, phrase('Unable to activate the theme with invalid package manifest.'), current_page('../', ['item' => null]));
        }

        if ('backend' == $package->type) {
            $target = 'backend_theme';
        } else {
            $target = 'frontend_theme';
        }

        $siteId = get_setting('id');

        $query = $this->model->update(
            'app__settings',
            [
                $target => $this->request->getPost('theme')
            ],
            [
                'id' => $siteId
            ]
        );

        if ($query) {
            return throw_exception(301, phrase('The selected theme was successfully activated.'), current_page('../', ['item' => null]));
        }

        return throw_exception(403, phrase('Unable to activate the selected theme.'), current_page('../', ['item' => null]));
    }

    /**
     * Customize theme
     */
    public function customize()
    {
        if (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json')) {
            return throw_exception(404, phrase('No theme package manifest were found.'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json'));

        if (! $package) {
            return throw_exception(403, phrase('Unable to customize the theme with invalid package manifest.'), current_page('../', ['item' => null]));
        }

        $package->folder = $this->_primary;
        $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));

        if ($this->validToken($this->request->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null]));
            } elseif (! is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'theme.json')) {
                return throw_exception(400, ['colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'theme.json ' . phrase('is not writable.')]);
            }

            $package->colorscheme = $this->request->getPost('colorscheme');
            $folder = $package->folder;

            unset($package->folder, $package->integrity);

            if (file_put_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'theme.json', json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE))) {
                return throw_exception(301, phrase('The theme was successfully customized.'), current_page('../', ['item' => null]));
            }

            return throw_exception(400, ['colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'theme.json ' . phrase('is not writable.')]);
        }

        $this->setTitle(phrase('Theme Customization'))
        ->setIcon('mdi mdi-palette')
        ->setOutput([
            'writable' => (is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'theme.json') ? true : false),
            'detail' => $package
        ])
        ->modalSize('modal-xl')

        ->render();
    }

    /**
     * Import theme
     */
    public function import()
    {
        if ($this->validToken($this->request->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../'));
            }

            $this->formValidation->setRule('file', phrase('Theme Package'), 'max_size[file,' . (MAX_UPLOAD_SIZE * 1024) . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip]|ext_in[file,zip]');

            if ($this->formValidation->run($this->request->getPost()) === false) {
                return throw_exception(400, $this->formValidation->getErrors());
            } elseif (empty($_FILES['file']['tmp_name'])) {
                return throw_exception(400, ['file' => phrase('No theme package were chosen.')]);
            } elseif (! class_exists('ZipArchive')) {
                return throw_exception(400, ['file' => phrase('No zip extension found on your web server configuration.')]);
            }

            $zip = new ZipArchive();
            $unzip = $zip->open($_FILES['file']['tmp_name']);
            $tmpPath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . sha1($_FILES['file']['tmp_name']);

            if (true === $unzip) {
                if (! is_dir($tmpPath) && ! mkdir($tmpPath, 0755, true)) {
                    return throw_exception(400, ['file' => phrase('Unable to extract your theme package.')]);
                }

                // Extract the repository
                $zip->extractTo($tmpPath);

                $files = directory_map($tmpPath);

                if (! $files) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmpPath);

                    return throw_exception(400, ['file' => phrase('Unable to extract your theme package.')]);
                }

                $validPackage = false;
                $packagePath = null;
                $extract = false;

                foreach ($files as $key => $val) {
                    if (! $packagePath) {
                        $packagePath = str_replace(DIRECTORY_SEPARATOR, '', $key);
                    }

                    if (! is_array($val)) {
                        continue;
                    }

                    foreach ($val as $_key => $_val) {
                        if (strpos($_key, ' ') !== false) {
                            break;
                        } elseif ('theme.json' == $_val && file_exists($tmpPath . DIRECTORY_SEPARATOR . $key . $_val)) {
                            $package = json_decode(file_get_contents($tmpPath . DIRECTORY_SEPARATOR . $key . $_val));

                            if (! $package || ! isset($package->name) || ! isset($package->description) || ! isset($package->version) || ! isset($package->author) || ! isset($package->compatibility) || ! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'], true)) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmpPath);

                                return throw_exception(400, ['file' => phrase('The package manifest was invalid.')]);
                            } elseif (! in_array(aksara('version'), $package->compatibility, true)) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmpPath);

                                return throw_exception(400, ['file' => phrase('This theme package is not compatible with your current Aksara version.')]);
                            }

                            $validPackage = true;
                        }
                    }
                }

                if (! $validPackage) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmpPath);

                    return throw_exception(400, ['file' => phrase('No package manifest found on your theme package.')]);
                }

                if (is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $packagePath) && ! $this->request->getPost('upgrade')) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmpPath);

                    return throw_exception(400, ['theme' => phrase('The theme package with same structure is already installed.')]);
                }

                if (is_writable(ROOTPATH . 'themes')) {
                    // Extract package contents
                    $extract = $zip->extractTo(ROOTPATH . 'themes');

                    // Close zip
                    $zip->close();
                }

                // Remove temporary directory
                $this->_rmdir($tmpPath);

                if ($extract && is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $packagePath)) {
                    return throw_exception(301, phrase('Your theme package was successfully imported.'), current_page('../'));
                } else {
                    return throw_exception(400, ['file' => phrase('Your theme folder seems cannot be writable.')]);
                }
            }

            return throw_exception(400, ['file' => phrase('Unable to extract the theme package.')]);
        }

        $this->setTitle(phrase('Theme Importer'))
        ->setIcon('mdi mdi-import')
        ->render();
    }

    /**
     * Delete theme
     */
    public function delete()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null]));
        }

        $this->permission->must_ajax(current_page('../', ['item' => null]));

        // Delete confirmation
        if (! $this->request->getPost('theme')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="--validate-form">
                    <div class="text-center">
                        ' . phrase('Are you sure want to delete this theme?') . '
                    </div>
                    <hr class="mx--3 border-secondary" />
                    <input type="hidden" name="theme" value="' . $this->_primary . '" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-light">
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

        $this->formValidation->setRule('theme', phrase('Theme'), 'required');

        if ($this->formValidation->run($this->request->getPost()) === false) {
            return throw_exception(400, ['theme' => $this->formValidation->getErrors()]);
        }

        //C heck if requested theme to delete is match
        if ($this->request->getPost('theme') && is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme'))) {
            if (DEMO_MODE) {
                return throw_exception(400, ['theme' => phrase('Changes will not saved in demo mode.')]);
            }

            // Check if theme property is exists
            if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme') . DIRECTORY_SEPARATOR . 'theme.json')) {
                $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme') . DIRECTORY_SEPARATOR . 'theme.json'));

                if (! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'], true)) {
                    return throw_exception(400, ['theme' => phrase('Unable to uninstall theme with invalid package.')]);
                }

                // Get the site id
                $siteId = get_setting('id');

                $activeTheme = $this->model->getWhere(
                    'app__settings',
                    [
                        'id' => $siteId
                    ],
                    1
                )
                ->row($package->type . '_theme');

                if ($this->request->getPost('theme') == $activeTheme) {
                    return throw_exception(400, ['theme' => phrase('Unable to uninstall the theme that is in use.')]);
                }

                // Delete theme
                $this->_rmdir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->request->getPost('theme'));
            } else {
                // Theme property is not found
                return throw_exception(400, ['theme' => phrase('A theme without package manifest cannot be uninstall from the theme manager.')]);
            }
        } else {
            return throw_exception(400, ['theme' => phrase('The theme you would to delete is not exists or already uninstalled.')]);
        }

        return throw_exception(301, phrase('The selected theme was successfully uninstalled.'), current_page('../', ['item' => null]));
    }

    /**
     * List installed themes
     */
    private function _installed()
    {
        $data = directory_map(ROOTPATH . 'themes');

        if (! $data) {
            return false;
        }

        $output = [];

        foreach ($data as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $_key => $_val) {
                    if ('theme.json' != $_val) {
                        continue;
                    }

                    $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $key . $_val));

                    if ($package) {
                        $package->folder = str_replace(DIRECTORY_SEPARATOR, '', $key);
                        $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));

                        $output[] = $package;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Remove directory recursivelly using
     *
     * @param mixed|null $directory
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
}
