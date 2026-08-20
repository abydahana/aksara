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
    }

    public function index()
    {
        if ($this->_primary && 'fetch' == $this->request->getPost('mode')) {
            return $this->_fetchProperties($this->_primary, $this->request->getPost('group'));
        }

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
                    'ip_address' => $this->request->getIPAddress(),
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
                    'ip_address' => $this->request->getIPAddress(),
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

            $auditFields = ['created_at', 'created_by', 'updated_at', 'updated_by'];

            // Fetch a sample row for data payloads (unformatted single row)
            $sampleRow = [];
            try {
                $sampleRequest = $curl->get(base_url($slug, ['limit' => 1]));
                $sampleDecoded = json_decode($sampleRequest->getBody());
                if (is_array($sampleDecoded) && isset($sampleDecoded[0])) {
                    $sampleRow = (array) $sampleDecoded[0];
                } elseif (is_object($sampleDecoded) && isset($sampleDecoded->results->table_data) && is_array($sampleDecoded->results->table_data) && isset($sampleDecoded->results->table_data[0])) {
                    $sampleRow = (array) $sampleDecoded->results->table_data[0];
                } elseif (is_object($sampleDecoded) && isset($sampleDecoded->results) && is_array($sampleDecoded->results) && isset($sampleDecoded->results[0])) {
                    $sampleRow = (array) $sampleDecoded->results[0];
                }
            } catch (Throwable $e) {
                // Fallback
            }

            foreach ($method as $key => $val) {
                $output[$val]['response'] = [
                    'success' => $exception,
                    'error' => $exception
                ];

                if (in_array($val, ['create', 'update'])) {
                    // Get field data
                    $request = $curl->get(base_url($slug . '/create', ['format_result' => 'metadata']));
                    $response = json_decode($request->getBody()) ?? [];

                    $fallbackRow = [];

                    foreach ($response as $field => $params) {
                        if ($params->hidden || in_array($field, $auditFields)) {
                            unset($response->$field);

                            continue;
                        }

                        if (in_array('required', $params->validation)) {
                            $response->$field->required = true;
                        }

                        $response->$field->type = array_keys((array) $params->type);

                        // Build fallback row value if sampleRow doesn't have this field
                        $typeKey = $response->$field->type[0] ?? 'text';
                        if (in_array($typeKey, ['number', 'integer', 'int', 'money', 'percent'])) {
                            $fallbackRow[$field] = 1;
                        } elseif (in_array($typeKey, ['boolean'])) {
                            $fallbackRow[$field] = true;
                        } elseif (in_array($typeKey, ['date'])) {
                            $fallbackRow[$field] = date('Y-m-d');
                        } elseif (in_array($typeKey, ['datetime', 'timestamp'])) {
                            $fallbackRow[$field] = date('Y-m-d H:i:s');
                        } else {
                            $fallbackRow[$field] = 'string';
                        }
                    }

                    $output[$val]['field_data'] = $response;

                    $payloadData = $sampleRow ?: $fallbackRow;

                    $output[$val]['response']['success'] = [
                        'code' => 200,
                        'message' => ('update' === $val ? phrase('The data was successfully updated.') : phrase('The data was successfully submitted.')),
                        'data' => $payloadData
                    ];
                } elseif (in_array($val, ['read'])) {
                    // Get field data for read
                    $request = $curl->get(base_url($slug, $sampleParams));
                    $response = json_decode($request->getBody());

                    if ('complete' === $responseType) {
                        $output[$val]['response']['success'] = $response ?? [];
                    } elseif (is_array($response) && isset($response[0])) {
                        $output[$val]['response']['success'] = $response[0];
                    } else {
                        $output[$val]['response']['success'] = $response ?? [];
                    }
                } elseif (! in_array($val, ['delete'])) {
                    // Get field data for index/other
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
