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

namespace Aksara\Laboratory;

use Aksara\Laboratory\Model;

class Permission
{
    private $_model;

    public function __construct()
    {
        $this->_model = new Model();
    }

    /**
     * API Authorization
     */
    public function authorize($username = '', $password = '')
    {
        $query = $this->_model->select('
            user_id,
            username,
            password,
            group_id,
            language_id,
            status
        ')
        ->where('username', $username)
        ->or_where('email', $username)
        ->get(
            'app__users',
            1
        )
        ->row();

        // Check if user is inactive
        if ($query && 1 != $query->status) {
            return throw_exception(400, ['username' => phrase('Your account is temporary disabled or not yet activated.')]);
        } elseif ($query && password_verify($password . ENCRYPTION_KEY, $query->password)) {
            // Check if login attempts failed from the previous session
            $blocking_check = $this->_model->get_where(
                'app__users_blocked',
                [
                    'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
                ],
                1
            )
            ->row();

            if ($blocking_check) {
                // Check if blocking time is still available
                if (strtotime($blocking_check->blocked_until) >= time()) {
                    // Throw the blocking messages
                    return throw_exception(400, ['username' => phrase('You are temporarily blocked due do frequent failed login attempts.')]);
                } else {
                    // Remove the record from blocking table
                    $this->_model->delete(
                        'app__users_blocked',
                        [
                            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
                        ]
                    );
                }
            }

            // Update the last login timestamp
            $this->_model->update(
                'app__users',
                [
                    'last_login' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $query->user_id
                ],
                1
            );

            // Check if system apply one device login
            if (get_setting('one_device_login')) {
                // Get older sessions
                $sessions = $this->_model->select('
                    session_id,
                    timestamp
                ')
                ->group_by('session_id')
                ->get_where(
                    'app__log_activities',
                    [
                        'user_id' => $query->user_id
                    ]
                )
                ->result();

                if ($sessions) {
                    // Older sessions exist
                    foreach ($sessions as $key => $val) {
                        if ($val->session_id && file_exists(WRITEPATH . 'session/' . $val->session_id)) {
                            // Older session found
                            try {
                                // Unlink older session
                                if (unlink(WRITEPATH . 'session/' . $val->session_id)) {
                                    // Update table to skip getting session_id on next execution
                                    $this->_model->update('app__log_activities', ['session_id' => ''], ['session_id' => $val->session_id]);
                                }
                            } catch (\Throwable $e) {
                                // Safe abstraction
                            }
                        }
                    }
                }
            }

            // Set the user credential into session
            set_userdata([
                'is_logged' => true,
                'user_id' => $query->user_id,
                'username' => $query->username,
                'group_id' => $query->group_id,
                'language_id' => $query->language_id,
                'session_generated' => time(),
                'access_token' => session_id()
            ]);

            if (service('request')->getPost('year')) {
                set_userdata('year', service('request')->getPost('year'));
            }

            // Get existing session
            $session_exists = $this->_model->get_where(
                'app__sessions',
                [
                    'id' => get_userdata('access_token')
                ]
            )
            ->row();

            if ($session_exists) {
                // Session exists, delete record
                $this->_model->delete(
                    'app__sessions',
                    [
                        'id' => get_userdata('access_token')
                    ]
                );
            }

            // Store session to database
            return $this->_model->insert(
                'app__sessions',
                [
                    'id' => get_userdata('access_token'),
                    'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => session_encode()
                ]
            );
        } else {
            return throw_exception(400, ['password' => phrase('Username or email and password combination does not match.')]);
        }
    }

    /**
     * Allow to accessing method
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     * @param   mixed|null $redirect
     */
    public function allow($path = null, $method = null, $user_id = 0, $redirect = null)
    {
        if (! $method || (! in_array($method, ['create', 'read', 'update', 'delete', 'export', 'print', 'pdf']) && ! method_exists(service('router')->controllerName(), $method))) {
            $method = 'index';
        } elseif ('clone' == $method) {
            $method = 'update';
        }

        $user = $this->_model->select('
            user_id,
            group_id
        ')
        ->get_where(
            'app__users',
            [
                'user_id' => ($user_id ? $user_id : service('session')->get('user_id')),
                'status' => 1
            ],
            1
        )
        ->row();

        if (! $user) {
            // Destroy previous session to prevent hijacking
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }

            return false;
        }

        $privileges = $this->_model->select('
            group_privileges
        ')
        ->get_where(
            'app__groups',
            [
                'group_id' => $user->group_id
            ],
            1
        )
        ->row('group_privileges');

        $privileges = json_decode($privileges, true);

        if (! isset($path, $privileges[$path]) || ! in_array($method, $privileges[$path])) {
            if (method_exists(service('router')->controllerName(), $method) || in_array($method, ['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf'])) {
                // Push to group privileges
                $this->_push_privileges($path, $method);
            }

            return false;
        } else {
            // Write log activities
            if ('modal' != service('request')->getPost('prefer')) {
                // Only if request is not from session storage
                $this->_push_logs($path, $method);
            }

            return true;
        }

        return false;
    }

    /**
     * Restrict for accessing method
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     * @param   mixed|null $redirect
     */
    public function restrict($path = null, $method = null, $redirect = null)
    {
        if (! $method || (! in_array($method, ['create', 'read', 'update', 'delete', 'export', 'print', 'pdf']) && ! method_exists(service('router')->controllerName(), $method))) {
            $method = 'index';
        } elseif ('clone' == $method) {
            $method = 'update';
        }

        $privileges = $this->_model->select('
            group_privileges
        ')
        ->get_where(
            'app__groups',
            [
                'group_id' => service('session')->get('group_id')
            ],
            1
        )
        ->row('group_privileges');

        $privileges = json_decode($privileges, true);

        if (isset($privileges[$path]) && in_array($method, $privileges[$path])) {
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page.'), ($redirect ? $redirect : base_url()));
        } else {
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page.'), ($redirect ? $redirect : base_url()));
        }
    }

    /**
     * Allow only access through ajax
     *
     * @param   mixed|null $redirect
     */
    public function must_ajax($redirect = null)
    {
        if (! service('request')->isAJAX()) {
            // Non AJAX, redirect to somewhere
            return throw_exception(403, phrase('You cannot perform the requested action.'), ($redirect ? $redirect : base_url()));
        }
    }

    /**
     * Store accessed method to privileges
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     */
    private function _push_privileges($path = null, $method = '')
    {
        $privileges = $this->_model->select('
            privileges
        ')
        ->get_where(
            'app__groups_privileges',
            [
                'path' => $path
            ],
            1
        )
        ->row('privileges');

        $privileges = ($privileges ? json_decode($privileges, true) : []);

        if ($privileges) {
            if (! in_array($method, $privileges)) {
                $privileges[] = $method;

                $prepare = [
                    'privileges' => json_encode($privileges),
                    'last_generated' => date('Y-m-d H:i:s')
                ];

                $this->_model->update(
                    'app__groups_privileges',
                    $prepare,
                    [
                        'path' => $path
                    ],
                    1
                );
            }
        } else {
            $checker = $this->_model->get_where(
                'app__groups_privileges',
                [
                    'path' => $path
                ],
                1
            )
            ->row();

            if (! $checker) {
                $privileges[] = $method;

                $prepare = [
                    'path' => $path,
                    'privileges' => json_encode($privileges),
                    'last_generated' => date('Y-m-d H:i:s')
                ];

                $this->_model->insert('app__groups_privileges', $prepare);
            }
        }
    }

    /**
     * Store activity logs into database
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     */
    private function _push_logs($path = null, $method = '')
    {
        $query = service('request')->getGet();

        unset($query['aksara']);

        $agent = service('request')->getUserAgent();

        if ($agent->isBrowser()) {
            $user_agent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $user_agent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $user_agent = $agent->getMobile();
        } else {
            $user_agent = phrase('Unknown');
        }

        $prepare = [
            'user_id' => service('session')->get('user_id'),
            'session_id' => COOKIE_NAME . session_id(),
            'path' => $path,
            'method' => $method,
            'query' => json_encode($query),
            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
            'browser' => $user_agent,
            'platform' => $agent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $this->_model->insert('app__log_activities', $prepare);
    }
}
