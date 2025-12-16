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

class Modules extends Core
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
    }

    public function index()
    {
        $this->set_title(phrase('Module Manager'))
        ->set_icon('mdi mdi-puzzle')
        ->set_output([
            'installed' => $this->_installed()
        ])

        ->render();
    }

    /**
     * Get module detail
     */
    public function detail()
    {
        $package = [];

        try {
            $package = json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json'));
        } catch (\Throwable $e) {
            // Safe abstraction
        }

        if ($package) {
            $package->folder = $this->_primary;
            $package->integrity = sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $this->set_title(phrase('Module Detail'))
        ->set_icon('mdi mdi-puzzle')
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
        if (! file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json')) {
            return throw_exception(404, phrase('The package manifest for the selected module is missing.'), current_page('../'));
        }

        $package = json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'theme.json'));

        if (! isset($package->hash) || ! isset($package->version)) {
            return throw_exception(404, phrase('The selected module is not supported by the official market.'), current_page('../', ['item' => null]));
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
                        'type' => 'module',
                        'initial' => $package->hash,
                        'version' => aksara('version')
                    ]
                ]
            );
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
        }

        $upstream = json_decode($response->getBody());

        if ($response->getStatusCode() !== 200) {
            return throw_exception(403, $response->getReasonPhrase(), current_page('../', ['item' => null]));
        } elseif (isset($upstream->version) && $upstream->version > $package->version) {
            $html = '
                <form action="' . current_page('../../../addons/install', ['item' => $upstream->path, 'type' => 'module']) . '" method="POST" class="--validate-form">
                    <div class="text-center">
                        ' . phrase('A new version of the selected module is available.') . '
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
     * Import module
     */
    public function import()
    {
        if ($this->valid_token($this->request->getPost('_token'))) {
            if (DEMO_MODE) {
                return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../'));
            }

            $this->form_validation->setRule('file', phrase('Module Package'), 'max_size[file,' . (MAX_UPLOAD_SIZE * 1024) . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip]|ext_in[file,zip]');

            if ($this->form_validation->run($this->request->getPost()) === false) {
                return throw_exception(400, $this->form_validation->getErrors());
            } elseif (empty($_FILES['file']['tmp_name'])) {
                return throw_exception(400, ['file' => phrase('No module package were chosen.')]);
            } elseif (! class_exists('ZipArchive')) {
                return throw_exception(400, ['file' => phrase('No zip extension found on your web server configuration.')]);
            }

            $zip = new \ZipArchive();
            $unzip = $zip->open($_FILES['file']['tmp_name']);
            $tmp_path = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . sha1($_FILES['file']['tmp_name']);
            $package = new \stdClass();

            if (true === $unzip) {
                if (! is_dir($tmp_path) && ! mkdir($tmp_path, 0755, true)) {
                    return throw_exception(400, ['file' => phrase('Unable to extract your module package.')]);
                }

                // Extract the repository
                $zip->extractTo($tmp_path);

                $files = directory_map($tmp_path);

                if (! $files) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmp_path);

                    return throw_exception(400, ['file' => phrase('Unable to extract your module package.')]);
                }

                $package = [];
                $valid_package = false;
                $package_path = null;
                $extract = false;

                foreach ($files as $key => $val) {
                    if (! $package_path && ! in_array($key, ['__MACOSX' . DIRECTORY_SEPARATOR])) {
                        $package_path = str_replace(DIRECTORY_SEPARATOR, '', $key);
                    }

                    if (! is_array($val)) {
                        continue;
                    }

                    foreach ($val as $_key => $_val) {
                        if (strpos($_key, ' ') !== false) {
                            break;
                        } elseif ('theme.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val)) {
                            $package = json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));

                            if (! $package || ! isset($package->name) || ! isset($package->description) || ! isset($package->version) || ! isset($package->author) || ! isset($package->compatibility) || ! isset($package->type) || ! in_array($package->type, ['module'])) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmp_path);

                                return throw_exception(400, ['file' => phrase('The package manifest was invalid.')]);
                            } elseif (! in_array(aksara('version'), $package->compatibility)) {
                                // Close the opened zip
                                $zip->close();

                                // Remove temporary directory
                                $this->_rmdir($tmp_path);

                                return throw_exception(400, ['file' => phrase('The package is not compatible with your current Aksara version.')]);
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

                    return throw_exception(400, ['file' => phrase('No package manifest found on your module package.')]);
                }

                if (is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path) && ! $this->request->getPost('upgrade')) {
                    // Close the opened zip
                    $zip->close();

                    // Remove temporary directory
                    $this->_rmdir($tmp_path);

                    return throw_exception(400, ['module' => phrase('This module package with same structure is already installed.')]);
                }

                if (is_writable(ROOTPATH . 'modules')) {
                    // Extract package contents
                    $extract = $zip->extractTo(ROOTPATH . 'modules');

                    // Close zip
                    $zip->close();
                }

                if ($extract && is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path)) {
                    try {
                        // Push module namespace to filelocator
                        $loader = Services::autoloader()->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);

                        // Run install migration
                        $migration = Services::migrations()->setNamespace('Modules\\' . $package_path);

                        // Trying to run the migration
                        if ($migration->latest()) {
                            //
                        }
                    } catch (\Throwable $e) {
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

                    return throw_exception(301, phrase('Your module package was successfully imported.'), current_page('../'));
                } else {
                    return throw_exception(400, ['file' => phrase('Your module folder seems cannot be writable.')]);
                }
            }

            return throw_exception(400, ['file' => phrase('Unable to extract the module package.')]);
        }

        $this->set_title(phrase('Module Importer'))
        ->set_icon('mdi mdi-import')
        ->render();
    }

    /**
     * Delete module
     */
    public function delete()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page('../', ['item' => null]));
        }

        $this->permission->must_ajax(current_page('../', ['item' => null]));

        // Delete confirmation
        if (! $this->request->getPost('module')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="--validate-form">
                    <div class="text-center">
                        ' . phrase('Are you sure want to delete this module?') . '
                    </div>
                    <hr class="mx--3 border-secondary" />
                    <input type="hidden" name="module" value="' . $this->_primary . '" />
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

        $this->form_validation->setRule('module', phrase('Module'), 'required');

        if ($this->form_validation->run($this->request->getPost()) === false) {
            return throw_exception(400, ['module' => $this->form_validation->getErrors()]);
        }

        // Check if requested module to delete is match
        if ($this->request->getPost('module') && is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->request->getPost('module'))) {
            if (DEMO_MODE) {
                return throw_exception(400, ['module' => phrase('Changes will not saved in demo mode.')]);
            }

            // Check if module property is exists
            if (file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->request->getPost('module') . DIRECTORY_SEPARATOR . 'theme.json')) {
                $query = $this->model->order_by('id', 'DESC')->get_where(
                    config('Migrations')->table,
                    [
                        'namespace' => 'Modules\\' . $this->request->getPost('module')
                    ],
                    1
                )
                ->row();

                if ($query) {
                    try {
                        // Push module namespace to filelocator
                        $loader = Services::autoloader()->addNamespace($query->namespace, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->request->getPost('module'));

                        // Run uninstall migration
                        $migration = Services::migrations()->setNamespace($query->namespace);

                        // Trying to run the migration
                        $migration->regress($query->batch);
                    } catch (\Throwable $e) {
                        return throw_exception(400, ['module' => $e->getMessage()]);
                    }
                }

                /**
                 * Prepare to remove unused privileges
                 */
                $package = file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->request->getPost('module') . DIRECTORY_SEPARATOR . 'theme.json');
                $package = json_decode($package);

                /**
                 * Prepare to update the menu that ever linked to uninstalled module before
                 */
                $query = $this->model->get_where(
                    'app__menus',
                    [
                    ]
                )
                ->result();

                // Check if query has result
                if ($query) {
                    // Query has result, loops the menus
                    foreach ($query as $key => $val) {
                        // Populate the menu as array
                        $menus = json_decode($val->serialized_data);

                        // Check if menus not empty
                        if ($menus) {
                            // Loops the menu to update links
                            foreach ($menus as $_key => $_val) {
                                // Check if the link id related to uninstalled module
                                if (isset($_val->id) && sha1($this->request->getPost('module')) == $_val->id) {
                                    // Link relate to uninstalled module, unset it
                                    unset($menus[$_key]);
                                }
                            }
                        }

                        // Update the menu structure
                        $this->model->update(
                            'app__menus',
                            [
                                'serialized_data' => json_encode($menus)
                            ],
                            [
                                'menu_id' => $val->menu_id
                            ]
                        );
                    }
                }

                // Check if package property is exists
                if (isset($package->permission) && is_object($package->permission)) {
                    // Package property exist, loops the permissions
                    foreach ($package->permission as $key => $val) {
                        // Get the privileges from the database
                        $privileges = $this->model->get_where(
                            'app__groups',
                            [
                                'group_id' => $key
                            ],
                            1
                        )
                        ->row('group_privileges');

                        // Populate the privileges
                        $privileges = json_decode($privileges, true);

                        // Check if privileges from database and module property is not empty
                        if ($privileges && $val) {
                            // Loops to unset the unused privileges
                            foreach ($val as $_key => $_val) {
                                // Unset unused privileges from group
                                unset($privileges[$_key]);

                                // Remove unused privileges
                                $this->model->delete(
                                    'app__groups_privileges',
                                    [
                                        'path' => $_key
                                    ]
                                );
                            }

                            // Update the privilege with new one
                            $this->model->update(
                                'app__groups',
                                [
                                    'group_privileges' => json_encode($privileges)
                                ],
                                [
                                    'group_id' => $key
                                ]
                            );
                        }
                    }
                }

                // Delete module
                $this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->request->getPost('module'));
            } else {
                // Module property is not found
                return throw_exception(400, ['module' => phrase('A module without package manifest cannot be uninstall from the module manager.')]);
            }
        } else {
            return throw_exception(400, ['module' => phrase('The module you would to delete is not exists or already uninstalled.')]);
        }

        return throw_exception(301, phrase('The selected module was successfully uninstalled.'), current_page('../', ['item' => null]));
    }

    /**
     * List installed modules
     */
    private function _installed()
    {
        $data = directory_map(ROOTPATH . 'modules');

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

                    $package = json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $key . $_val));

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
