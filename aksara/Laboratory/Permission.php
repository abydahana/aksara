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
     * Allow to accessing method
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     * @param   mixed|null $redirect
     */
    public function allow($path = null, $method = null, $user_id = 0, $redirect = null)
    {
        if (! $method || ! method_exists(service('router')->controllerName(), $method)) {
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
        if (! $method) {
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
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page'), ($redirect ? $redirect : base_url()));
        } else {
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page'), ($redirect ? $redirect : base_url()));
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
            return throw_exception(403, phrase('You cannot perform the requested action'), ($redirect ? $redirect : base_url()));
        }
    }

    /**
     * Store accessed method to privileges
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     */
    private function _push_privileges($path = null, $method = null)
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
    private function _push_logs($path = null, $method = null)
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
