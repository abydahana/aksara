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

namespace Aksara\Modules\APIs\Controllers;

use Throwable;
use Config\Services;
use Aksara\Laboratory\Core;

class Documentation extends Core
{
    private ?string $_primary;
    private array $_collection = [];
    private array $_namespace = [];

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');
        $this->setMethod('index');

        $this->_primary = $this->request->getGet('slug');

        if ($this->_primary && 'fetch' == $this->request->getPost('mode')) {
            return $this->_fetchProperties($this->_primary, $this->request->getPost('group'));
        }
    }

    public function index()
    {
        $this->setTitle(phrase('API Documentations'))
        ->setIcon('mdi mdi-book-open-page-variant')

        ->setOutput([
            'modules' => $this->_scanModule(),
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
                'app_groups',
                [
                    'status' => 1
                ]
            )
            ->result();

            if ($query) {
                $groups = $query;
            }

            $query = $this->model->getWhere(
                'app_groups_privileges',
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

    private function _fetchProperties($slug = null, $groupId = 0)
    {
        if (in_array($slug, $this->_restrictedResource())) {
            return false;
        }

        $method = $this->request->getPost('method');
        $responseType = $this->request->getPost('response_type');
        $title = $slug;
        $output = [];
        $sessionId = session_id();
        $session = get_userdata();
        $fetchError = null;

        if (! $slug || ! $method) {
            return false;
        }

        if (! in_array($responseType, ['simple', 'complete'])) {
            $responseType = 'simple';
        }

        $sampleParams = [
            'format_result' => ('complete' == $responseType ? 'complete' : null),
            'limit' => 1
        ];
        $sampleParams = array_filter($sampleParams, fn ($value) => null !== $value);

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
            'app_sessions',
            [
                'id' => $sessionId
            ]
        )
        ->row();

        if ($tmpSession) {
            // Temporary session exists, update it
            $this->model->update(
                'app_sessions',
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
                'app_sessions',
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

                if (in_array($val, ['create', 'update'])) {
                    // Get field data
                    $request = $curl->get(base_url($slug . '/create', ['format_result' => 'field_data']));
                    $response = json_decode($request->getBody()) ?? [];

                    foreach ($response as $field => $params) {
                        if ($params->hidden) {
                            unset($response->$field);

                            continue;
                        }

                        if (in_array('required', $params->validation)) {
                            $response->$field->required = true;
                        }

                        $response->$field->type = array_keys((array) $params->type);
                    }

                    $output[$val]['field_data'] = $response;
                } elseif (in_array($val, ['read'])) {
                    // Get field data
                    $request = $curl->get(base_url($slug, $sampleParams));
                    $response = json_decode($request->getBody());

                    if (isset($response[0])) {
                        $output[$val]['response']['success'] = $response[0];
                    }
                } elseif (! in_array($val, ['delete'])) {
                    // Get field data
                    $request = $curl->get(base_url($slug, $sampleParams));
                    $response = json_decode($request->getBody());

                    $output[$val]['response']['success'] = $response ?? [];
                }

                if (in_array($val, ['read', 'update', 'delete', 'export', 'print', 'pdf'])) {
                    $request = $curl->get(base_url($slug, ['format_result' => 'full', 'limit' => 1]));
                    $response = json_decode($request->getBody());

                    if (isset($response->results->table_data[0]->primary)) {
                        $output[$val]['query_params'] = $response->results->table_data[0]->primary;
                    }
                }




                /*
                // Call API request
                $request = $curl->get(base_url($slug . (! in_array($val, ['index', 'delete']) ? '/' . $val : null), ['format_result' => 'full', 'limit' => 1]));

                // Decode response
                $response = json_decode($request->getBody());

                if (isset($response->method)) {
                    if (in_array($response->method, ['index'])) {
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
                    } elseif (in_array($response->method, ['create', 'update'])) {
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
                                    'required' => in_array('required', (array)$_val->validation)
                                ];

                                if (in_array('required', $_val->validation)) {
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
                    } elseif (in_array($response->method, ['read'])) {
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

                if (isset($response->method) && in_array($response->method, ['create', 'update', 'delete'])) {
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
            $fetchError = $e->getMessage();
        }

        // Restore the session
        set_userdata([
            'is_logged' => (isset($session['is_logged']) ? $session['is_logged'] : 0),
            'group_id' => (isset($session['group_id']) ? $session['group_id'] : 0)
        ]);

        if ($tmpSession) {
            // Restore existing session data
            $this->model->update(
                'app_sessions',
                [
                    'ip_address' => $tmpSession->ip_address,
                    'timestamp' => $tmpSession->timestamp,
                    'data' => $tmpSession->data
                ],
                [
                    'id' => $sessionId
                ]
            );
        } else {
            // Remove the temporary session
            $this->model->delete(
                'app_sessions',
                [
                    'id' => $sessionId
                ]
            );
        }

        if (isset($output['export'])) {
            $output['export']['response']['success'] = phrase('Binary file');
        }
        if (isset($output['print'])) {
            $output['print']['response']['success'] = phrase('HTML file');
        }
        if (isset($output['pdf'])) {
            $output['pdf']['response']['success'] = phrase('Binary file');
        }

        $response = [
            'title' => $title,
            'results' => $output
        ];

        if ($fetchError) {
            $response['error'] = $fetchError;
        }

        return make_json($response);
    }

    private function _scanModule()
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

                if (! in_array($slug, $this->_restrictedResource())) {
                    $this->_collection[] = $slug;
                    $this->_namespace[$slug] = $namespace;
                }
            }
        }
    }

    private function _restrictedResource()
    {
        return ['administrative/updater', 'assets', 'assets/svg', 'pages/blank', 'shortlink', 'xhr', 'xhr/boot', 'xhr/language', 'xhr/partial', 'xhr/partial/account', 'xhr/partial/announcement', 'xhr/partial/language', 'xhr/summernote', 'xhr/widget/comment'];
    }
}
