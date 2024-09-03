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

class Documentation extends \Aksara\Laboratory\Core
{
    private $_primary;

    private $_collection = [];
    private $_namespace = [];

    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');
        $this->set_method('index');

        $this->_primary = service('request')->getGet('slug');

        if ($this->_primary && 'fetch' == service('request')->getPost('mode')) {
            return $this->_fetch_properties($this->_primary, service('request')->getPost('group'));
        }
    }

    public function index()
    {
        $this->set_title(phrase('API Documentations'))
        ->set_icon('mdi mdi-book-open-page-variant')

        ->set_output([
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
                'group_privileges' => json_encode(
                    [
                        $slug => ['index']
                    ]
                )

            ]
        ];

        $privileges = ['index'];

        if ($slug) {
            $query = $this->model->like([
                'group_privileges' => '"' . $slug . '"'
            ])
            ->or_like([
                'group_privileges' => '"' . str_replace('/', '\/', $slug) . '"'
            ])
            ->get_where(
                'app__groups',
                [
                    'status' => 1
                ]
            )
            ->result();

            if ($query) {
                $groups = $query;
            }

            $query = $this->model->get_where(
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

    private function _fetch_properties($slug = null, $group_id = 0)
    {
        if (in_array($slug, $this->_restricted_resource())) {
            return false;
        }

        $method = service('request')->getPost('method');
        $title = $slug;
        $output = [];
        $session_id = session_id();
        $session = get_userdata();

        if (! $slug || ! $method) {
            return false;
        }

        if (get_userdata('group_id') != $group_id) {
            set_userdata('group_id', $group_id);
        }

        $exception = [
            'code' => phrase('HTTP status code'),
            'message' => phrase('Exception messages'),
            'target' => phrase('Redirect URL')
        ];

        // Check the temporary session
        $tmp_session = $this->model->get_where(
            'app__sessions',
            [
                'id' => $session_id
            ]
        )
        ->num_rows();

        if ($tmp_session) {
            // Temporary session exists, update it
            $this->model->update(
                'app__sessions',
                [
                    'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => (DB_DRIVER === 'Postgre' ? '\x' . bin2hex(session_encode()) : session_encode())
                ],
                [
                    'id' => $session_id
                ]
            );
        } else {
            // Store temporary session
            $this->model->insert(
                'app__sessions',
                [
                    'id' => $session_id,
                    'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => (DB_DRIVER === 'Postgre' ? '\x' . bin2hex(session_encode()) : session_encode())
                ]
            );
        }

        try {
            // Prepare the cURL
            $curl = \Config\Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 3
                ],
                'headers' => [
                    'X-API-KEY' => ENCRYPTION_KEY,
                    'X-ACCESS-TOKEN' => $session_id
                ]
            ]);

            foreach ($method as $key => $val) {
                // Make a request
                $request = $curl->get(base_url($slug . ('delete' == $val ? '/update' : ('index' != $val ? '/' . $val : null)), ['limit' => 1]));

                // Decode the response
                $response = json_decode($request->getBody());

                // Push response
                $output[$val]['response']['success'] = $response ?? trim($request->getHeaderLine('Content-Type'));
                $output[$val]['response']['error'] = $exception;

                if (isset($response->method) && 'update' === $response->method) {
                    // Make a request
                    $request = $curl->get(base_url($slug . '/create'));

                    // Decode the response
                    $response = json_decode($request->getBody());
                }

                if (isset($response->method) && in_array($response->method, ['create', 'update']) && isset($response->results->field_data) && 'delete' != $val) {
                    $output[$val]['parameter'] = $response->results->field_data;

                    $validation_error = [];

                    foreach ($response->results->field_data as $_key => $_val) {
                        if ($_val->required) {
                            $validation_error[$_key] = phrase('Validation messages');
                        }
                    }

                    $output[$val]['response']['error'] = [
                        'status' => 400,
                        'message' => $validation_error
                    ];
                }

                if (isset($response->results->query_string) && (isset($response->method) && in_array($response->method, ['read', 'update', 'delete']) || in_array($val, ['create', 'update', 'delete']))) {
                    $output[$val]['query_string'] = $response->results->query_string;
                }

                if (isset($response->method) && in_array($response->method, ['create', 'update', 'delete']) || in_array($val, ['create', 'update', 'delete'])) {
                    $output[$val]['response']['success'] = [
                        'code' => phrase('HTTP status code'),
                        'message' => phrase('Success messages'),
                        'target' => phrase('Redirect URL')
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Safe abstraction
        }

        // Remove the temporary session
        $this->model->delete(
            'app__sessions',
            [
                'id' => $session_id
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

    private function _scandir($parent_dir = null, $scandir = [], $namespace = null)
    {
        foreach ($scandir as $key => $val) {
            if (is_array($val)) {
                $this->_scandir($parent_dir . (! is_numeric($key) ? $key : null), $val, $key);
            } else {
                $namespace = $namespace . $val;
                $val = '/' . str_replace(['\\', '.php'], ['/', ''], strtolower($parent_dir . (! is_numeric($key) ? $key : null) . $val));

                $find_duplicate = array_reverse(explode('/', $val));

                $is_duplicate = (isset($find_duplicate[0]) && isset($find_duplicate[1]) && $find_duplicate[0] == $find_duplicate[1] ? true : false);

                if (! $is_duplicate) {
                    $slug = ltrim(rtrim($val, '/'), '/');
                } else {
                    $slug = ltrim(rtrim('/' . str_replace(['\\', '.php'], ['/', ''], strtolower($parent_dir . (! is_numeric($key) ? $key : null))), '/'), '/');
                }

                if (! in_array($slug, $this->_restricted_resource())) {
                    $this->_collection[] = $slug;
                    $this->_namespace[$slug] = $namespace;
                }
            }
        }
    }

    private function _restricted_resource()
    {
        return ['administrative/updater', 'assets', 'assets/svg', 'pages/blank', 'shortlink', 'xhr', 'xhr/boot', 'xhr/language', 'xhr/partial', 'xhr/partial/account', 'xhr/partial/language', 'xhr/summernote'];
    }
}
