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

class Addons extends Core
{
    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        helper('filesystem');

        $this->_primary = $this->request->getGet('item');

        if ('market' == $this->request->getPost('source')) {
            return $this->_listing();
        }
    }

    public function index()
    {
        $this->set_title(phrase('Add-Ons Market'))
        ->set_icon('mdi mdi-cart')

        ->render();
    }

    /**
     * Grab add-ons detail from Aksara Market
     */
    public function detail()
    {
        $package = [];

        if (! function_exists('curl_init') || ! function_exists('curl_exec')) {
            return throw_exception(403, phrase('The cURL module is not enabled.'), current_page('../'));
        } elseif (! @fsockopen('www.aksaracms.com', 443)) {
            return throw_exception(403, phrase('Unable to connect to the Aksara Market.'), current_page('../'));
        }

        if (! $package) {
            try {
                $curl = Services::curlrequest(
                    [
                        'timeout' => 5,
                        'http_errors' => false
                    ]
                );

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

            if ($response->getStatusCode() !== 200) {
                return throw_exception(403, $response->getReasonPhrase(), current_page('../'));
            }
        }

        $this->set_title((isset($package->name) ? $package->name : phrase('No item found!')))
        ->set_icon('mdi ' . ($this->request->getGet('type') == 'theme' ? 'mdi-palette' : 'mdi-puzzle'))
        ->set_output([
            'detail' => $package
        ])
        ->modal_size('modal-xl')

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
        } elseif (! @fsockopen('www.aksaracms.com', 443)) {
            return [
                'error' => phrase('Unable to connect to the Aksara Market.')
            ];
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
                // Get update package from remote server
                $tmp_path = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->request->getGet('item');

                // Check if temporary path is available
                if (! is_dir($tmp_path)) {
                    try {
                        // Try create temporary path
                        mkdir($tmp_path, 0755, true);

                        // Copy the repository to temporary path
                        if (! copy($package->repository, $tmp_path . DIRECTORY_SEPARATOR . 'file.zip')) {
                            // Unable to copy file, use FTP instead
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
                                return throw_exception(404, phrase('You need to set up an FTP connection to update your core system due the server does not appear to be writable.'), go_to('ftp'));
                            }

                            $encrypter = Services::encrypter();

                            // Configuration found, decrypt password
                            $query->username = $encrypter->decrypt(base64_decode($query->username));
                            $query->password = $encrypter->decrypt(base64_decode($query->password));

                            // Try to connect to FTP
                            $connection = ftp_connect($query->hostname, $query->port, 10);

                            if (! $connection || ! ftp_login($connection, $query->username, $query->password)) {
                                return throw_exception(403, phrase('Unable to connect to the FTP using the provided configuration.'));
                            }

                            // Download file over FTP
                            ftp_get($connection, $package->repository, $tmp_path . DIRECTORY_SEPARATOR . 'file.zip', FTP_BINARY);

                            // Close FTP connection
                            ftp_close($connection);
                        }
                    } catch (Throwable $e) {
                        // Action error, throw exception
                        return throw_exception(403, $response->getReasonPhrase(), go_to());
                    }
                }

                // Load the zip class
                $zip = new ZipArchive();

                // Unzip the repository
                $unzip = $zip->open($tmp_path . DIRECTORY_SEPARATOR . 'file.zip');

                if (true === $unzip) {
                    // Extract the repository
                    $zip->extractTo($tmp_path);

                    $files = directory_map($tmp_path);

                    if (! $files) {
                        // Close opened zip
                        $zip->close();

                        return throw_exception(400, ['file' => phrase('Unable to extract the selected ' . $type . ' package.')]);
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
                            if ('theme.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val)) {
                                $package = json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));

                                if (! $package || ! isset($package->name) || ! isset($package->description) || ! isset($package->version) || ! isset($package->author) || ! isset($package->compatibility) || ! isset($package->type) || ! in_array($package->type, ['module', 'backend', 'frontend'])) {
                                    // Close opened zip
                                    $zip->close();

                                    // Remove temporary directory
                                    $this->_rmdir($tmp_path);

                                    return throw_exception(403, phrase('The package manifest was invalid.'));
                                } elseif (! in_array(aksara('version'), $package->compatibility)) {
                                    // Close opened zip
                                    $zip->close();

                                    // Remove temporary directory
                                    $this->_rmdir($tmp_path);

                                    return throw_exception(403, phrase('The {{type}} package is not compatible with your current Aksara version.', ['type' => $type]));
                                }

                                $valid_package = true;
                            }
                        }
                    }

                    if (! $valid_package) {
                        // Close opened zip
                        $zip->close();

                        // Remove temporary directory
                        $this->_rmdir($tmp_path);

                        return throw_exception(403, phrase('No package manifest found on your module package.'));
                    }

                    // Check if the directory already exists
                    if (is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $package_path) && $this->request->getPost('upgrade') != $this->request->getGet('item')) {
                        // Close opened zip
                        $zip->close();

                        // Remove temporary directory
                        $this->_rmdir($tmp_path);

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

                    if (is_writable(ROOTPATH . $path)) {
                        // Extract package contents
                        $extract = $zip->extractTo(ROOTPATH . $path);

                        // Close zip
                        $zip->close();
                    }

                    if ($extract && is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $package_path)) {
                        try {
                            $autoloader = Services::autoloader();

                            // Push module namespace to filelocator
                            $loader = $autoloader->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);

                            // Run install migration
                            $migration = Services::migrations()->setNameSpace('Modules\\' . $package_path);

                            // Trying to run the migration
                            if ($migration->latest()) {
                                //
                            }
                        } catch (Throwable $e) {
                            // Migration error, delete module
                            $this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);

                            return throw_exception(400, ['file' => $e->getMessage()]);
                        }

                        // Run additional command to assign the menus or permissions
                        if (isset($package->menu) && is_array($package->menu)) {
                            // Assign the available menus
                            foreach ($package->menu as $key => $val) {
                                // Check if theme property contain valid menu
                                if (! isset($val->placement) || ! in_array($val->placement, ['header', 'sidebar']) || ! isset($val->group) || ! isset($val->link) || ! is_array($val->link) || ! $val->link) {
                                    continue;
                                }

                                // Check if given group is on valid array
                                if (! is_array($val->group)) {
                                    // Otherwise, convert group as array
                                    $val->group = [$val->group];
                                }

                                // Populate given links as array with adding the unique id
                                $links = str_replace('"label":"', '"id":"' . sha1($package_path) . '","label":"', json_encode($val->link));
                                $links = json_decode($links, true);

                                // Check if links is available or continue
                                if (! $links) {
                                    continue;
                                }

                                // Loops the given group
                                foreach ($val->group as $_key => $_val) {
                                    // Get the existing menu from the database
                                    $existing = $this->model->get_where(
                                        'app__menus',
                                        [
                                            'menu_placement' => $val->placement,
                                            'group_id' => $_val
                                        ],
                                        1
                                    )
                                    ->row();

                                    // Populate the link obtained
                                    $serialized = (isset($existing->serialized_data) ? $existing->serialized_data : '[]');
                                    $serialized = ($serialized ? json_decode($serialized, true) : []);

                                    // Check if obtained links is populated
                                    if ($serialized) {
                                        // Make links unique
                                        $serialized = $this->_array_unique($serialized, 'slug', $package_path);

                                        // Merge the old link with new one
                                        $links = array_merge($serialized, $links);
                                    }

                                    if ($existing) {
                                        // Update the menu to the database
                                        $this->model->update(
                                            'app__menus',
                                            [
                                                'serialized_data' => json_encode($links)
                                            ],
                                            [
                                                'menu_id' => $existing->menu_id
                                            ]
                                        );
                                    } else {
                                        // Insert the menu to the database
                                        $this->model->insert(
                                            'app__menus',
                                            [
                                                'menu_placement' => $val->placement,
                                                'menu_label' => phrase('Generated Menu'),
                                                'menu_description' => phrase('Generated menu from module installation.'),
                                                'serialized_data' => json_encode($links),
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
                                    $privileges = $this->model->get_where(
                                        'app__groups_privileges',
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
                                            'app__groups_privileges',
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
                                            'app__groups_privileges',
                                            [
                                                'path' => $_key,
                                                'privileges' => json_encode(array_unique($_val)),
                                                'last_generated' => date('Y-m-d H:i:s')
                                            ]
                                        );
                                    }
                                }

                                // Get the existing group privileges
                                $group_privileges = $this->model->get_where(
                                    'app__groups',
                                    [
                                        'group_id' => $key
                                    ],
                                    1
                                )
                                ->row('group_privileges');

                                // Check if group privileges has result
                                if ($group_privileges) {
                                    // Update the group privileges obtained
                                    $this->model->update(
                                        'app__groups',
                                        [
                                            'group_privileges' => json_encode(array_merge(json_decode($group_privileges, true), json_decode(json_encode($val), true)))
                                        ],
                                        [
                                            'group_id' => $key
                                        ]
                                    );
                                }
                            }
                        }

                        // Remove temporary directory
                        $this->_rmdir($tmp_path);

                        return throw_exception(301, phrase('The selected {{type}} package was successfully installed.', ['type' => $type]), current_page('../' . $type, ['item' => null, 'type' => null]));
                    } else {
                        return throw_exception(403, phrase('Your {{type}} folder seems not writable.', ['type' => $type]));
                    }
                }

                // Remove temporary directory
                $this->_rmdir($tmp_path);

                return throw_exception(403, phrase('Unable to install the selected {{type}}.', ['type' => $type]));
            }

            return throw_exception(404, phrase('The selected {{type}} you would to install is not available.', ['type' => $type]));
        }

        return throw_exception(404, phrase('Your web server need to connected to the internet to install the add-ons.'));
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
        } elseif (! @fsockopen('www.aksaracms.com', 443)) {
            return make_json([
                'error' => phrase('Unable to connect to the Aksara Market.')
            ]);
        }

        $themes = directory_map(ROOTPATH . 'themes', 1);
        $installed_themes = [];

        if ($themes) {
            foreach ($themes as $key => $val) {
                if (strpos($val, DIRECTORY_SEPARATOR) !== false) {
                    $installed_themes[] = str_replace(DIRECTORY_SEPARATOR, '', $val);
                }
            }
        }

        $modules = directory_map(ROOTPATH . 'modules', 1);
        $installed_modules = [];

        if ($modules) {
            foreach ($modules as $key => $val) {
                if (strpos($val, DIRECTORY_SEPARATOR) !== false) {
                    $installed_modules[] = str_replace(DIRECTORY_SEPARATOR, '', $val);
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
                        'installed' => json_encode([
                            'themes' => $installed_themes,
                            'modules' => $installed_modules
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

    /**
     * Make array unique by value
     *
     * @param mixed|null $key
     * @param mixed|null $value
     */
    private function _array_unique($array = [], $key = null, $value = null)
    {
        $value = strtolower($value);

        foreach ($array as $subKey => $subArray) {
            if (isset($subArray[$key]) && $subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }

        return $array;
    }
}
