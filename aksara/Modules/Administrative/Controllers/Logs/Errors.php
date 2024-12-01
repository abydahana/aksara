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

namespace Aksara\Modules\Administrative\Controllers\Logs;

class Errors extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');
    }

    public function index()
    {
        $this->set_title(phrase('Error Logs'))
        ->set_icon('mdi mdi-bug')

        ->set_output([
            'logs' => $this->_logs(),
            'report' => $this->_report()
        ])

        ->render();
    }

    public function remove()
    {
        $this->permission->must_ajax();

        if (! service('request')->getPost('log')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="--validate-form">
                    <div class="text-center mb-3">
                        ' . phrase('Are you sure want to delete this log?') . '
                    </div>
                    <input type="hidden" name="log" value="' . service('request')->getGet('log') . '" />
                    <hr class="mx--3 border-secondary-subtle" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">
                                    <i class="mdi mdi-window-close"></i>
                                    ' . phrase('Cancel') . '
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill">
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
                    'popup' => true
                ],
                'content' => $html
            ]);
        }

        $filename = basename(service('request')->getPost('log'));

        try {
            unlink(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename);

            $deleted = true;
        } catch (\Throwable $e) {
            $deleted = false;
        }

        if ($deleted) {
            return throw_exception(301, phrase('The selected log was successfully removed'), current_page('../', ['log' => null]));
        }

        return throw_exception(403, phrase('Unable to remove the selected log'), current_page('../', ['log' => null]));
    }

    public function clear()
    {
        $this->permission->must_ajax();

        if (! service('request')->getPost('confirm')) {
            $html = '
                <form action="' . current_page() . '" method="POST" class="--validate-form">
                    <div class="text-center mb-3">
                        <i class="mdi mdi-help-rhombus-outline mdi-5x text-danger"></i>
                        <br />
                        <h5>
                            ' . phrase('Are you sure want to clear the logs?') . '
                        </h5>
                        <p class="lead">
                            ' . phrase('This action will remove everything related to the error logs') . '
                        </p>
                    </div>
                    <input type="hidden" name="confirm" value="1" />
                    <hr class="mx--3 border-secondary-subtle" />
                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">
                                    <i class="mdi mdi-window-close"></i>
                                    ' . phrase('Cancel') . '
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill">
                                    <i class="mdi mdi-check"></i>
                                    ' . phrase('Clear Logs') . '
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'popup' => true
                ],
                'content' => $html
            ]);
        }

        /* load required helper */
        helper('filesystem');

        $data = directory_map(WRITEPATH . 'logs', 1);
        $logs = [];
        $success = 0;

        if ($data) {
            foreach ($data as $key => $val) {
                if (pathinfo($val, PATHINFO_EXTENSION) != 'log') {
                    continue;
                }

                $logs[] = $val;
            }
        }

        if ($logs) {
            foreach ($logs as $key => $val) {
                try {
                    unlink(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $val);

                    $success++;
                } catch (\Throwable $e) {
                }
            }
        }

        return throw_exception(301, '<b>' . number_format($success) . '</b> ' . phrase('of') . ' <b>' . number_format(sizeof($logs)) . '</b> ' . phrase('was successfully removed'), current_page('../', ['log' => null]));
    }

    private function _logs()
    {
        /* load required helper */
        helper('filesystem');

        $data = directory_map(WRITEPATH . 'logs', 1);
        $logs = [];

        if ($data) {
            foreach ($data as $key => $val) {
                if (pathinfo($val, PATHINFO_EXTENSION) != 'log') {
                    continue;
                }

                $logs[] = $val;
            }
        }

        // Sort DESC
        usort($logs, function ($a, $b) {
            $dateA = \DateTime::createFromFormat('Y-m-d', substr($a, 4, 10));
            $dateB = \DateTime::createFromFormat('Y-m-d', substr($b, 4, 10));
            return $dateB <=> $dateA;
        });

        return $logs;
    }

    private function _report()
    {
        if (! service('request')->getGet('report')) {
            return false;
        }

        $filename = basename(service('request')->getGet('report'));

        if (file_exists(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename) && is_readable(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename)) {
            $report = htmlspecialchars(file_get_contents(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename));
            $report = explode("\n", $report);

            return $report;
        }

        return false;
    }
}
