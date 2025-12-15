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

use Throwable;
use Config\Services;
use Aksara\Laboratory\Model;

/**
 * Permission Class
 *
 * Handles user authentication, authorization, access control lists (ACL),
 * and activity logging.
 */
class Permission
{
    /**
     * Database model instance
     * @var Model
     */
    private $_model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_model = new Model();
    }

    /**
     * Authenticate user credentials and establish session.
     * Handles login throttling, blocking, and single-device login policy.
     *
     * @param   string $username
     * @param   string $password
     */
    public function authorize($username = '', $password = '')
    {
        $request = Services::request();

        // Retrieve user by username or email
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

        // Validate user status and password
        if ($query && 1 != $query->status) {
            // User exists but inactive
            return throw_exception(400, ['username' => phrase('Your account is temporary disabled or not yet activated.')]);
        } elseif ($query && password_verify($password . ENCRYPTION_KEY, $query->password)) {
            // Security: Check for brute force blocking (based on IP)
            $blocking_check = $this->_model->get_where(
                'app__users_blocked',
                [
                    'ip_address' => ($request->hasHeader('x-forwarded-for') ? $request->getHeaderLine('x-forwarded-for') : $request->getIPAddress())
                ],
                1
            )
            ->row();

            if ($blocking_check) {
                // Check if blocking time is still active
                if (strtotime($blocking_check->blocked_until) >= time()) {
                    return throw_exception(400, ['username' => phrase('You are temporarily blocked due do frequent failed login attempts.')]);
                } else {
                    // Release the block if time passed
                    $this->_model->delete(
                        'app__users_blocked',
                        [
                            'ip_address' => ($request->hasHeader('x-forwarded-for') ? $request->getHeaderLine('x-forwarded-for') : $request->getIPAddress())
                        ]
                    );
                }
            }

            // Update last login timestamp
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

            // Policy: One Device Login (Optional)
            if (get_setting('one_device_login')) {
                // Retrieve active sessions for this user
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
                    // Iterate and destroy older sessions
                    foreach ($sessions as $key => $val) {
                        if ($val->session_id && file_exists(WRITEPATH . 'session/' . $val->session_id)) {
                            try {
                                // Force logout other device
                                if (unlink(WRITEPATH . 'session/' . $val->session_id)) {
                                    // Remove session reference from logs
                                    $this->_model->update('app__log_activities', ['session_id' => ''], ['session_id' => $val->session_id]);
                                }
                            } catch (Throwable $e) {
                                // Ignore filesystem errors
                            }
                        }
                    }
                }
            }

            // Set current session data
            set_userdata([
                'is_logged' => true,
                'user_id' => $query->user_id,
                'username' => $query->username,
                'group_id' => $query->group_id,
                'language_id' => $query->language_id,
                'session_generated' => time(),
                'access_token' => session_id()
            ]);

            // Optional: Store financial year if provided
            if ($request->getPost('year')) {
                set_userdata('year', $request->getPost('year'));
            }

            // Manage Session Table (Prevent duplication)
            $session_exists = $this->_model->get_where(
                'app__sessions',
                [
                    'id' => get_userdata('access_token')
                ]
            )
            ->row();

            if ($session_exists) {
                $this->_model->delete(
                    'app__sessions',
                    [
                        'id' => get_userdata('access_token')
                    ]
                );
            }

            // Store new session to database
            return $this->_model->insert(
                'app__sessions',
                [
                    'id' => get_userdata('access_token'),
                    'ip_address' => ($request->hasHeader('x-forwarded-for') ? $request->getHeaderLine('x-forwarded-for') : $request->getIPAddress()),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'data' => session_encode()
                ]
            );
        } else {
            // Password mismatch or user not found
            return throw_exception(400, ['password' => phrase('Username or email and password combination does not match.')]);
        }
    }

    /**
     * Check if the user is allowed to access the requested path and method.
     * Also handles automatic privilege registration for unknown paths.
     *
     * @param   string|null $path
     * @param   string|null $method
     * @param   int         $user_id
     * @param   string|null $redirect
     * @return  bool
     */
    public function allow($path = null, $method = null, $user_id = 0, $redirect = null)
    {
        $router = Services::router();

        // Normalize method name
        if (! $method || (! in_array($method, ['create', 'read', 'update', 'delete', 'export', 'print', 'pdf']) && ! method_exists($router->controllerName(), $method))) {
            $method = 'index';
        } elseif ('clone' == $method) {
            $method = 'update';
        }

        // Identify the user
        $user = $this->_model->select('
            user_id,
            group_id
        ')
        ->get_where(
            'app__users',
            [
                'user_id' => ($user_id ? $user_id : get_userdata('user_id')),
                'status' => 1
            ],
            1
        )
        ->row();

        if (! $user) {
            // User invalid/not logged in, destroy session
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }

            return false;
        }

        // Fetch privileges for the user's group
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

        // Check access rights
        if (! isset($path, $privileges[$path]) || ! in_array($method, $privileges[$path])) {
            // Access Denied

            // Auto-Discovery: If method exists but privilege not registered, register it
            if (method_exists($router->controllerName(), $method) || in_array($method, ['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf'])) {
                $this->_push_privileges($path, $method);
            }

            return false;
        } else {
            // Access Granted
            $request = Services::request();

            // Log activity (exclude modal requests)
            if ('modal' != $request->getPost('prefer')) {
                $this->_push_logs($path, $method);
            }

            return true;
        }

        return false;
    }

    /**
     * Enforce access restriction.
     * Throws an exception if the user does not have sufficient privileges.
     *
     * @param   string|null $path
     * @param   string|null $method
     * @param   string|null $redirect
     * @return  mixed|void
     */
    public function restrict($path = null, $method = null, $redirect = null)
    {
        $router = Services::router();

        // Normalize method
        if (! $method || (! in_array($method, ['create', 'read', 'update', 'delete', 'export', 'print', 'pdf']) && ! method_exists($router->controllerName(), $method))) {
            $method = 'index';
        } elseif ('clone' == $method) {
            $method = 'update';
        }

        // Fetch privileges
        $privileges = $this->_model->select('
            group_privileges
        ')
        ->get_where(
            'app__groups',
            [
                'group_id' => get_userdata('group_id')
            ],
            1
        )
        ->row('group_privileges');

        $privileges = json_decode($privileges, true);

        // Verify access
        // Check if privilege is NOT set or method is NOT in the allowed list
        if (! isset($privileges[$path]) || ! in_array($method, $privileges[$path])) {
            // Access DENIED
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page.'), ($redirect ? $redirect : base_url()));
        }
    }

    /**
     * Ensure the request is made via AJAX.
     * Throws 403 exception if accessed via standard HTTP request.
     *
     * @param   string|null $redirect
     * @return  void|mixed
     */
    public function must_ajax($redirect = null)
    {
        $request = Services::request();

        if (! $request->isAJAX()) {
            // Block non-AJAX requests
            return throw_exception(403, phrase('You cannot perform the requested action.'), ($redirect ? $redirect : base_url()));
        }
    }

    /**
     * Register a new path or method to the privileges table.
     * Used for auto-discovery of new features/controllers.
     *
     * @param   string|null $path
     * @param   string      $method
     */
    private function _push_privileges($path = null, $method = '')
    {
        // Get existing privileges for the path
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
            // Path exists, append method if missing
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
            // Path does not exist, check existence before insert
            $checker = $this->_model->get_where(
                'app__groups_privileges',
                [
                    'path' => $path
                ],
                1
            )
            ->row();

            if (! $checker) {
                // Initialize new path privilege
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
     * Record user activity logs including IP, browser, and platform.
     *
     * @param   string|null $path
     * @param   string      $method
     */
    private function _push_logs($path = null, $method = '')
    {
        $request = Services::request();

        // Prepare query string (remove token)
        $query = $request->getGet();
        unset($query['aksara']);

        // Parse User Agent
        $agent = $request->getUserAgent();

        if ($agent->isBrowser()) {
            $user_agent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $user_agent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $user_agent = $agent->getMobile();
        } else {
            $user_agent = phrase('Unknown');
        }

        // Prepare Log Data
        $prepare = [
            'user_id' => get_userdata('user_id'),
            'session_id' => COOKIE_NAME . session_id(),
            'path' => $path,
            'method' => $method,
            'query' => json_encode($query),
            'ip_address' => ($request->hasHeader('x-forwarded-for') ? $request->getHeaderLine('x-forwarded-for') : $request->getIPAddress()),
            'browser' => $user_agent,
            'platform' => $agent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Save to Database
        $this->_model->insert('app__log_activities', $prepare);
    }
}
