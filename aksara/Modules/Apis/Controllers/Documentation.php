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

namespace Aksara\Modules\Apis\Controllers;

use Config\Services;
use Aksara\Laboratory\Core;
use Throwable;
use stdClass;

class Documentation extends Core
{
    private $_primary;

    private $_collection = [];
    private $_namespace = [];

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');
        $this->setMethod('index');

        $this->_primary = $this->request->getGet('slug');

        if ($this->_primary && 'fetch' == $this->request->getPost('mode')) {
            return $this->_fetch_properties($this->_primary, $this->request->getPost('group'));
        }
    }

    public function index()
    {
        $this->setTitle(phrase('API Documentations'))
        ->setIcon('mdi mdi-book-open-page-variant')

        ->setOutput([
            'modules' => $this->_scan_module(),
            'permission' => $this->_permission($this->_primary),
            'active' => $this->_primary
        ])

        ->render();
    }

    private function _permission($slug = null)
    {
        $groups = [
            [
                'group_id' => 0,
                'group_name' => phrase('Public'),
                'group_description' => null,
                'group_privileges' => json_encode([
                    $slug => ['index']
                ])

            ]
        ];

        $privileges = ['index'];

        if ($slug) {
            $query = $this->model->like([
                'group_privileges' => '"' . $slug . '"'
            ])
            ->orLike([
                'group_privileges' => '"' . str_replace('/', '\/', $slug) . '"'
            ])
            ->getWhere(
                'app__groups',
                [
                    'status' => 1
                ]
            )
            ->result();

            if ($query) {
                $groups = $query;
            }

            $query = $this->model->getWhere(
                'app__groups_privileges',
                [
                    'path' => $slug
                ],
                1
            )
            ->row('privileges');

            if ($query) {
                $privileges = json_decode($query);
            }
        }

        return [
            'groups' => $groups,
            'privileges' => $privileges
        ];
    }

    private function _fetch_properties($slug = null, $groupId = 0)
    {
        if (in_array($slug, $this->_restricted_resource(), true)) {
            return false;
        }

        $method = $this->request->getPost('method');
        $title = $slug;
        $output = [];
        $sessionId = session_id();
        $session = get_userdata();

        if (! $slug || ! $method) {
            return false;
        }

        if (get_userdata('group_id') != $groupId) {
            set_userdata('group_id', $groupId);
        }

        $exception = [
            'code' => phrase('HTTP status code'),
            'message' => phrase('Exception messages'),
            'target' => phrase('Redirect URL')
        ];

        // Check the temporary session
        $tmpSession = $this->model->getWhere(
            'app__sessions',
            [
                'id' => $sessionId
            ]
        )
        ->numRows();

        if ($tmpSession) {
            // Temporary session exists, update it
            $this->model->update(
                'app__sessions',
                [
                    'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => (DB_DRIVER === 'Postgre' ? '\x' . bin2hex(session_encode()) : session_encode())
                ],
                [
                    'id' => $sessionId
                ]
            );
        } else {
            // Store temporary session
            $this->model->insert(
                'app__sessions',
                [
                    'id' => $sessionId,
                    'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => (DB_DRIVER === 'Postgre' ? '\x' . bin2hex(session_encode()) : session_encode())
                ]
            );
        }

        try {
            // Prepare the cURL
            $curl = Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 3
                ],
                'headers' => [
                    'X-API-KEY' => ENCRYPTION_KEY,
                    'X-ACCESS-TOKEN' => $sessionId
                ]
            ]);

            foreach ($method as $key => $val) {
                $output[$val]['response'] = [
                    'success' => $exception,
                    'error' => $exception
                ];

                if (in_array($val, ['create', 'update'], true)) {
                    // Get field data
                    $request = $curl->get(base_url($slug . '/create', ['format_result' => 'field_data']));
                    $response = json_decode($request->getBody()) ?? [];

                    foreach ($response as $field => $params) {
                        if ($params->hidden) {
                            unset($response->$field);

                            continue;
                        }

                        if (in_array('required', $params->validation, true)) {
                            $response->$field->required = true;
                        }

                        $response->$field->type = array_keys((array) $params->type);
                    }

                    $output[$val]['field_data'] = $response;
                } elseif (in_array($val, ['read'], true)) {
                    // Get field data
                    $request = $curl->get(base_url($slug, ['limit' => 1]));
                    $response = json_decode($request->getBody());

                    if (isset($response[0])) {
                        $output[$val]['response']['success'] = $response[0];
                    }
                } elseif (! in_array($val, ['delete'], true)) {
                    // Get field data
                    $request = $curl->get(base_url($slug, ['limit' => 1]));
                    $response = json_decode($request->getBody());

                    $output[$val]['response']['success'] = $response ?? [];
                }

                if (in_array($val, ['read', 'update', 'delete', 'export', 'print', 'pdf'], true)) {
                    $request = $curl->get(base_url($slug, ['format_result' => 'full', 'limit' => 1]));
                    $response = json_decode($request->getBody());

                    if (isset($response->results->table_data[0]->primary)) {
                        $output[$val]['query_params'] = $response->results->table_data[0]->primary;
                    }
                }




                /*
                // Call API request
                $request = $curl->get(base_url($slug . (! in_array($val, ['index', 'delete'], true) ? '/' . $val : null), ['format_result' => 'full', 'limit' => 1]));

                // Decode response
                $response = json_decode($request->getBody());

                if (isset($response->method)) {
                    if (in_array($response->method, ['index'], true)) {
                        // Push response
                        $output[$val]['response']['success'] = trim($request->getHeaderLine('Content-Type'));
                        $output[$val]['response']['error'] = $exception;

                        if (isset($response->results->table_data[0])) {
                            $fieldData = [];

                            foreach($response->results->table_data[0]->field_data as $_key => $_val) {
                                $fieldData[$_key] = $_val->content;
                            }

                            $output[$val]['response']['success'] = $fieldData;
                        }
                    } elseif (in_array($response->method, ['create', 'update'], true)) {
                        $request = $curl->get(base_url($slug . '/create', ['format_result' => 'field_data']));

                        // Decode the response
                        $response = json_decode($request->getBody());

                        if (isset($response[0])) {
                            // Set field data
                            $fieldData = [];
                            $validationError = [];

                            foreach ($response[0] as $_key => $_val) {
                                if ($_val->hidden) continue;

                                $fieldData[$_key] = [
                                    'type' => array_keys((array)$_val->type),
                                    'maxlength' => $_val->maxlength,
                                    'label' => $_key,
                                    'required' => in_array('required', (array)$_val->validation, true)
                                ];

                                if (in_array('required', $_val->validation, true)) {
                                    // Set field validation
                                    $validationError[$_key] = phrase('Validation messages');
                                }
                            }

                            $output[$val]['field_data'] = $fieldData;
                            $output[$val]['response']['success'] = $exception;
                            $output[$val]['response']['error'] = [
                                'status' => 400,
                                'message' => $validationError
                            ];
                        }
                    } elseif (in_array($response->method, ['read'], true)) {
                        $request = $curl->get(base_url($slug . '/create', ['format_result' => 'field_data']));

                        // Decode the response
                        $response = json_decode($request->getBody());

                        if (isset($response[0])) {
                        }
                        if (isset($response->results->table_data[0])) {
                            $fieldData = [];

                            foreach($response->results->table_data[0]->field_data as $_key => $_val) {
                                $fieldData[$_key] = $_val->content;
                            }

                            $output[$val]['response']['success'] = $fieldData;

                            // Set query params
                            $output[$val]['query_params'] = $response->results->table_data[0]->primary;
                        }
                    }
                }

                if (isset($response->method) && in_array($response->method, ['create', 'update', 'delete'], true)) {
                    // Set exception message
                    $output[$val]['response']['success'] = [
                        'code' => phrase('HTTP status code'),
                        'message' => phrase('Success messages'),
                        'target' => phrase('Redirect URL')
                    ];
                }
                    */
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
            exit;
            // Safe abstraction
        }

        // Remove the temporary session
        $this->model->delete(
            'app__sessions',
            [
                'id' => $sessionId
            ]
        );

        // Restore the session
        set_userdata([
            'is_logged' => (isset($session['is_logged']) ? $session['is_logged'] : 0),
            'group_id' => (isset($session['group_id']) ? $session['group_id'] : 0)
        ]);

        if (isset($output['export'])) {
            $output['export']['response']['success'] = phrase('Binary file');
        }
        if (isset($output['print'])) {
            $output['print']['response']['success'] = phrase('HTML file');
        }
        if (isset($output['pdf'])) {
            $output['pdf']['response']['success'] = phrase('Binary file');
        }

        return make_json([
            'title' => $title,
            'results' => $output
        ]);
    }

    private function _scan_module()
    {
        helper('filesystem');

        $modules = [];
        $scandir = array_merge(directory_map('..' . DIRECTORY_SEPARATOR . 'aksara' . DIRECTORY_SEPARATOR . 'Modules'), directory_map('..' . DIRECTORY_SEPARATOR . 'modules'));

        if ($scandir) {
            foreach ($scandir as $key => $val) {
                if (isset($val['Controllers' . DIRECTORY_SEPARATOR]) && is_array($val['Controllers' . DIRECTORY_SEPARATOR])) {
                    $this->_scandir($key, $val['Controllers' . DIRECTORY_SEPARATOR]);
                }
            }
        }

        if ($this->_collection) {
            sort($this->_collection);
        }

        return $this->_collection;
    }

    private function _scandir($parentDir = null, $scandir = [], $namespace = null)
    {
        foreach ($scandir as $key => $val) {
            if (is_array($val)) {
                $this->_scandir($parentDir . (! is_numeric($key) ? $key : null), $val, $key);
            } else {
                $namespace = $namespace . $val;
                $val = '/' . str_replace(['\\', '.php'], ['/', ''], strtolower($parentDir . (! is_numeric($key) ? $key : null) . $val));

                $findDuplicate = array_reverse(explode('/', $val));

                $isDuplicate = (isset($findDuplicate[0]) && isset($findDuplicate[1]) && $findDuplicate[0] == $findDuplicate[1] ? true : false);

                if (! $isDuplicate) {
                    $slug = ltrim(rtrim($val, '/'), '/');
                } else {
                    $slug = ltrim(rtrim('/' . str_replace(['\\', '.php'], ['/', ''], strtolower($parentDir . (! is_numeric($key) ? $key : null))), '/'), '/');
                }

                if (! in_array($slug, $this->_restricted_resource(), true)) {
                    $this->_collection[] = $slug;
                    $this->_namespace[$slug] = $namespace;
                }
            }
        }
    }

    private function _restricted_resource()
    {
        return ['administrative/updater', 'assets', 'assets/svg', 'pages/blank', 'shortlink', 'xhr', 'xhr/boot', 'xhr/language', 'xhr/partial', 'xhr/partial/account', 'xhr/partial/announcement', 'xhr/partial/language', 'xhr/summernote', 'xhr/widget/comment'];
    }
}
