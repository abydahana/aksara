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

class Themes extends \Aksara\Laboratory\Core
{
    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        helper('filesystem');

        $this->_primary = service('request')->getGet('item');
    }

    public function index()
    {
        $this->set_title(phrase('Theme Manager'))
        ->set_icon('mdi mdi-palette')
        ->set_output([
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
            $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'));
        } catch(\Throwable $e) {
            // Safe abstraction
        }

        if ($package) {
            $package->folder = $this->_primary;
            $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $this->set_title(phrase('Theme Detail'))
        ->set_icon('mdi mdi-palette')
        ->set_output([
            'detail' => $package
        ])
        ->modal_size('modal-xl')

        ->render();
    }

    /**
     * Update module
     */
    public function update()
    {
        if (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json')) {
            return throw_exception(404, phrase('The package manifest for the selected theme is missing'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'));

        if (! isset($package->hash) || ! isset($package->version)) {
            return throw_exception(404, phrase('The selected theme is not supported by the official market'), current_page('../', ['item' => null]));
        }

        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return throw_exception(403, phrase('The cURL module is not enabled'), current_page('../', ['item' => null]));
        } elseif (! @fsockopen('www.aksaracms.com', 443)) {
            return throw_exception(403, phrase('Unable to connect to the Aksara Market'), current_page('../', ['item' => null]));
        }

        try {
            $curl = \Config\Services::curlrequest([
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
        } catch(\Throwable $e) {
            return throw_excetion(404, $e->getMessage());
        }

        $upstream = json_decode($response->getBody());

        if ($response->getStatusCode() !== 200) {
            return throw_exception(403, $response->getReason(), current_page('../', ['item' => null]));
        } elseif (isset($upstream->version) && $upstream->version > $package->version) {
            $html = '
                <form action="' . current_page('../../../addons/install', ['item' => $upstream->path, 'type' => 'theme']) . '" method="POST" class="p-3 --validate-form">
                    <div class="text-center">
                        ' . phrase('A new version of the selected theme is available') . '
                        <br />
                        <h4>
                            ' . $upstream->name . '
                        </h4>
                        <h5>
                            '. phrase('Version') . ' ' . $upstream->version . '
                        </h5>
                    </div>
                    <hr class="row" />
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
                'html' => $html
            ]);
        }

        return throw_exception(404, phrase('No update available at the moment'), current_page('../', ['item' => null]));
    }

    /**
     * Activate theme
     */
    public function activate()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode'), current_page('../', ['item' => null]));
        }

        $this->permission->must_ajax(current_page('../', ['item' => null]));

        if (! service('request')->getPost('theme')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="p-3 --validate-form">
                    <div class="text-center">
                        ' . phrase('Are you sure want to activate this theme?') . '
                    </div>
                    <hr class="row" />
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
                'html' => $html
            ]);
        }

        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode'), current_page('../', ['item' => null]));
        } elseif (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json')) {
            return throw_exception(404, phrase('No theme package manifest were found'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json'));

        if (! $package || ! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'])) {
            return throw_exception(403, phrase('Unable to activate the theme with invalid package manifest'), current_page('../', ['item' => null]));
        }

        if ('backend' == $package->type) {
            $target = 'backend_theme';
        } else {
            $target = 'frontend_theme';
        }

        $site_id = get_setting('id');

        $query = $this->model->update(
            'app__settings',
            [
                $target => service('request')->getPost('theme')
            ],
            [
                'id' => $site_id
            ]
        );

        if ($query) {
            return throw_exception(301, phrase('The selected theme was successfully activated'), current_page('../', ['item' => null]));
        }

        return throw_exception(403, phrase('Unable to activate the selected theme'), current_page('../', ['item' => null]));
    }

    /**
     * Customize theme
     */
    public function customize()
    {
        if (! file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json')) {
            return throw_exception(404, phrase('No theme package manifest were found'), current_page('../', ['item' => null]));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'));

        if (! $package) {
            return throw_exception(403, phrase('Unable to customize the theme with invalid package manifest'), current_page('../', ['item' => null]));
        }

        $package->folder = $this->_primary;
        $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));

        if ($this->valid_token(service('request')->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(404, phrase('Changes will not saved in demo mode'), current_page('../', ['item' => null]));
            } elseif (! is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json')) {
                return throw_exception(400, ['colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json ' . phrase('is not writable')]);
            }

            $package->colorscheme = service('request')->getPost('colorscheme');
            $folder = $package->folder;

            unset($package->folder, $package->integrity);

            if (file_put_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'package.json', json_encode($package))) {
                return throw_exception(301, phrase('The theme was successfully customized'), current_page('../', ['item' => null]));
            }

            return throw_exception(400, ['colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'package.json ' . phrase('is not writable')]);
        }

        $this->set_title(phrase('Theme Customization'))
        ->set_icon('mdi mdi-palette')
        ->set_output([
            'writable' => (is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json') ? true : false),
            'detail' => $package
        ])
        ->modal_size('modal-xl')

        ->render();
    }

    /**
     * Import theme
     */
    public function import()
    {
        if ($this->valid_token(service('request')->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(404, phrase('Changes will not saved in demo mode'), current_page('../'));
            }

            $this->form_validation->setRule('file', phrase('Theme Package'), 'max_size[file,' . MAX_UPLOAD_SIZE . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip]|ext_in[file,zip]');

            if ($this->form_validation->run(service('request')->getPost()) === false) {
                return throw_exception(400, $this->form_validation->getErrors());
            } elseif (empty($_FILES['file']['tmp_name'])) {
                return throw_exception(400, ['file' => phrase('No theme package were chosen')]);
            } elseif (! class_exists('ZipArchive')) {
                return throw_exception(400, ['file' => phrase('No zip extension found on your web server configuration')]);
            }

            $zip = new \ZipArchive();
            $unzip = $zip->open($_FILES['file']['tmp_name']);
            $tmp_path = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . sha1($_FILES['file']['tmp_name']);

            if (true === $unzip) {
                if (! is_dir($tmp_path) && ! mkdir($tmp_path, 0755, true)) {
                    return throw_exception(400, ['file' => phrase('Unable to extract your theme package')]);
                }

                // Extract the repository
                $zip->extractTo($tmp_path);

                $files = directory_map($tmp_path);

                if (! $files) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmp_path);

                    return throw_exception(400, ['file' => phrase('Unable to extract your theme package')]);
                }

                $valid_package = false;
                $package_path = null;
                $extract = false;

                foreach ($files as $key => $val) {
                    if (! $package_path) {
                        $package_path = str_replace(DIRECTORY_SEPARATOR, '', $key);
                    }

                    if (! is_array($val)) {
                        continue;
                    }

                    foreach ($val as $_key => $_val) {
                        if (strpos($_key, ' ') !== false) {
                            break;
                        } elseif ('package.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val)) {
                            $package = json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));

                            if (! $package || ! isset($package->name) || ! isset($package->description) || ! isset($package->version) || ! isset($package->author) || ! isset($package->compatibility) || ! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'])) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmp_path);

                                return throw_exception(400, ['file' => phrase('The package manifest was invalid')]);
                            } elseif (! in_array(aksara('version'), $package->compatibility)) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmp_path);

                                return throw_exception(400, ['file' => phrase('This theme package is not compatible with your current aksara version')]);
                            }

                            $valid_package = true;
                        }
                    }
                }

                if (! $valid_package) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmp_path);

                    return throw_exception(400, ['file' => phrase('No package manifest found on your theme package')]);
                }

                if (is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package_path) && ! service('request')->getPost('upgrade')) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmp_path);

                    return throw_exception(400, ['theme' => phrase('The theme package with same structure is already installed')]);
                }

                if (is_writable(ROOTPATH . 'themes')) {
                    $extract = $zip->extractTo(ROOTPATH . 'themes');

                    $zip->close();
                } else {
                    // Get the site id
                    $site_id = get_setting('id');

                    $query = $this->model->get_where(
                        'app__ftp',
                        [
                            'site_id' => $site_id
                        ],
                        1
                    )
                    ->row();

                    if (! $query) {
                        return throw_exception(404, phrase('You need to set up an FTP connection to update your core system due the server does not appear to be writable'), go_to('../../ftp'));
                    }

                    /* configuration found, decrypt password */
                    $query->username = service('encrypter')->decrypt(base64_decode($query->username));
                    $query->password = service('encrypter')->decrypt(base64_decode($query->password));

                    // Try to connect to FTP
                    $connection = @ftp_connect($query->hostname, $query->port, 10);

                    if (! $connection || ! @ftp_login($connection, $query->username, $query->password)) {
                        return throw_exception(403, phrase('Unable to connect to the FTP using the provided configuration'));
                    }

                    $extract = $zip->extractTo(ROOTPATH . 'themes');

                    $zip->close();
                }

                // Remove temporary directory
                $this->_rmdir($tmp_path);

                if ($extract && is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package_path)) {
                    return throw_exception(301, phrase('Your theme package was successfully imported'), current_page('../'));
                } else {
                    return throw_exception(400, ['file' => phrase('Your theme folder seems cannot be writable')]);
                }
            }

            return throw_exception(400, ['file' => phrase('Unable to extract the theme package')]);
        }

        $this->set_title(phrase('Theme Importer'))
        ->set_icon('mdi mdi-import')
        ->render();
    }

    /**
     * Delete theme
     */
    public function delete()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode'), current_page('../', ['item' => null]));
        }

        $this->permission->must_ajax(current_page('../', ['item' => null]));

        /* delete confirmation */
        if (! service('request')->getPost('theme')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="p-3 --validate-form">
                    <div class="text-center">
                        ' . phrase('Are you sure want to delete this theme?') . '
                    </div>
                    <hr class="row" />
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
                'html' => $html
            ]);
        }

        $this->form_validation->setRule('theme', phrase('Theme'), 'required');

        if ($this->form_validation->run(service('request')->getPost()) === false) {
            return throw_exception(400, ['theme' => $this->form_validation->getErrors()]);
        }

        /* check if requested theme to delete is match */
        if (service('request')->getPost('theme') && is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme'))) {
            if (DEMO_MODE) {
                return throw_exception(400, ['theme' => phrase('Changes will not saved in demo mode')]);
            }

            /* check if theme property is exists */
            if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json')) {
                $package = json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json'));

                if (! isset($package->type) || ! in_array($package->type, ['backend', 'frontend'])) {
                    return throw_exception(400, ['theme' => phrase('Unable to uninstall theme with invalid package')]);
                }

                // Get the site id
                $site_id = get_setting('id');

                $active_theme = $this->model->get_where(
                    'app__settings',
                    [
                        'id' => $site_id
                    ],
                    1
                )
                ->row($package->type . '_theme');

                if (service('request')->getPost('theme') == $active_theme) {
                    return throw_exception(400, ['theme' => phrase('Unable to uninstall the theme that is in use')]);
                }

                /* delete theme */
                $this->_rmdir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme'));
            } else {
                /* theme property is not found */
                return throw_exception(400, ['theme' => phrase('A theme without package manifest cannot be uninstall from the theme manager')]);
            }
        } else {
            return throw_exception(400, ['theme' => phrase('The theme you would to delete is not exists or already uninstalled')]);
        }

        return throw_exception(301, phrase('The selected theme was successfully uninstalled'), current_page('../', ['item' => null]));
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
                    if ('package.json' != $_val) {
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
            /* delete directory */
            if (! delete_files($directory, true)) {
                /* Unable to delete directory. Get FTP configuration */
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
                    /* configuration found, decrypt password */
                    $query->username = service('encrypter')->decrypt(base64_decode($query->username));
                    $query->password = service('encrypter')->decrypt(base64_decode($query->password));

                    try {
                        /* trying to delete directory using ftp instead */
                        $connection = ftp_connect($query->hostname, $query->port, 10);

                        if ($connection && ftp_login($connection, $query->username, $query->password)) {
                            /* Yay! FTP is connected, try to delete the directory */
                            $this->_ftp_rmdir($connection, $directory);

                            /* close FTP connection */
                            ftp_close($connection);
                        }
                    } catch(\Throwable $e) {
                        return throw_exception(400, ['file' => $e->getMessage()]);
                    }
                }
            } elseif (is_dir($directory)) {
                // Remove garbage directory
                rmdir($directory);
            }
        }
    }

    /**
     * Remove directory and its files using FTP
     *
     * @param mixed|null $connection
     * @param mixed|null $directory
     */
    private function _ftp_rmdir($connection = null, $directory = null)
    {
        if (! $directory) {
            return false;
        }

        $lists = ftp_mlsd($connection, $directory);

        unset($lists[0]);
        unset($lists[1]);

        foreach ($lists as $list) {
            $full = $directory . DIRECTORY_SEPARATOR . $list['name'];

            if ('dir' == $list['type']) {
                // Directory found, reinitialize
                $this->_ftp_rmdir($connection, $full);
            } else {
                // Delete file
                ftp_delete($connection, $full);
            }
        }

        // Delete directory
        ftp_rmdir($connection, $directory);
    }
}
