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

namespace Aksara\Modules\Administrative\Controllers\Groups;

use Aksara\Laboratory\Core;

class Groups extends Core
{
    private $_table = 'app__groups';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->unsetDelete('group_id', [1, 2, 3]);
    }

    public function index()
    {
        $this->setTitle(phrase('Manage Groups'))
        ->setIcon('mdi mdi-key')
        ->setDescription('
            <div class="row">
                <div class="col-12">
                    ' . phrase('The listed privileges below is created automatically from') . '
                    &nbsp;
                    <a href="' . go_to('privileges', ['q' => null, 'per_page' => null, 'group_id' => null, 'path' => null]) . '" class="badge bg-secondary --xhr">
                        <i class="mdi mdi-arrow-right"></i>
                        ' . phrase('Privileges Adjustment') . '
                    </a>
                </div>
            </div>
        ')
        ->unsetColumn('group_id, group_privileges')
        ->unsetView('group_id')
        ->unsetField('group_id')
        ->setField([
            'group_description' => 'textarea',
            'status' => 'boolean'
        ])
        ->setField('group_privileges', 'custom_format', 'formatPrivileges')
        ->setValidation([
            'group_name' => 'required',
            'group_description' => 'required'
        ])
        ->setAlias([
            'group_name' => phrase('Group Name'),
            'group_description' => phrase('Description'),
            'group_privileges' => phrase('Privileges'),
            'status' => phrase('Status')
        ])

        ->modalSize('modal-lg')

        ->orderBy('group_id')

        ->render($this->_table);
    }

    protected function formatPrivileges(array $data)
    {
        $modulesCollection = $this->model->select('
            path,
            privileges
        ')
        ->orderBy('path')
        ->get('app__groups_privileges')
        ->result();

        $current = ($data['group_privileges'] ? json_decode($data['group_privileges'], true) : []);
        $output = null;

        if ($modulesCollection) {
            $modules = [];

            foreach ($modulesCollection as $key => $val) {
                $path = str_replace('/', '__', $val->path);
                $privilegeOutput = null;
                $privileges = ($val->privileges ? json_decode($val->privileges) : []);

                if (! $privileges) {
                    continue;
                }

                foreach ($privileges as $key => $privilege) {
                    if ('index' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('create' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('read' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('update' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('delete' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('export' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('print' == $privilege) {
                        $label = phrase(ucfirst($privilege));
                    } elseif ('pdf' == $privilege) {
                        $label = phrase(strtoupper($privilege));
                    } else {
                        $label = phrase(ucwords(str_replace('_', ' ', $privilege)));
                    }

                    if ('read' === $this->getMethod()) {
                        $privilegeOutput .= '
                            <div class="col-6 col-md-3">
                                <label class="d-block mb-0"' . (strlen($label) > 12 ? ' data-bs-toggle="tooltip" title="' . $label . '"' : null) . '>
                                    <i class="fw-bold mdi ' . (isset($current[$val->path]) && in_array($privilege, $current[$val->path], true) ? 'mdi-checkbox-marked-circle text-primary' : 'mdi-checkbox-blank-circle-outline') . '"></i> ' . $label . '
                                </label>
                            </div>
                        ';
                    } else {
                        $privilegeOutput .= '
                            <div class="col-6 col-md-3 bg-white">
                                <div class="form-check form-switch mb-0"' . (strlen($label) > 12 ? ' data-bs-toggle="tooltip" title="' . $label . '"' : null) . '>
                                    <input type="checkbox" name="group_privileges[' . $val->path . '][]" value="' . $privilege . '" class="form-check-input checker-children" id="' . $path . '_' . $privilege . '"' . (isset($current[$val->path]) && in_array($privilege, $current[$val->path], true) ? ' checked' : '') . ' />
                                    <label class="form-check-label" for="' . $path . '_' . $privilege . '">
                                        ' . $label . '
                                    </label>
                                </div>
                            </div>
                        ';
                    }
                }

                $paths = explode('/', $val->path);
                list($module) = array_pad($paths, 1, null);
                $modulePath = null;

                foreach ($paths as $_key => $_val) {
                    $modulePath .= ($_key ? ' &gt; ' : null) . phrase(ucwords(str_replace('_', ' ', $_val)));
                }

                $output .= '
                    ' . (! in_array($module, $modules, true) ? '
                    ' . ($modules ? '<hr />' : null) . '
                    <a href="' . base_url($val->path) . '" target="_blank">
                        <b>
                            <i class="mdi mdi-puzzle-outline"></i>
                            &nbsp;
                            ' . phrase('Module') . ': ' . phrase(ucwords(str_replace('_', ' ', $module))) . '
                        </b>
                    </a>
                    ' : '') . '
                    <div class="check-group w-100">
                        <div class="' . (in_array($this->getMethod(), ['create', 'update'], true) ? 'form-check form-switch' : null) . '">
                            ' . (in_array($this->getMethod(), ['create', 'update'], true) ? '<input type="checkbox" class="form-check-input" id="' . $path . '" data-bs-toggle="tooltip" title="' . phrase('Check all') . '" role="checker" data-parent=".check-group" />' : null) . '
                            <label class="fw-bold" for="' . $path . '">
                                ' . $modulePath . '
                            </label>
                            <a href="' . base_url($val->path) . '" target="_blank">
                                <i class="mdi mdi-launch"></i>
                            </a>
                        </div>
                        <div class="row mb-3">
                            ' . $privilegeOutput . '
                        </div>
                    </div>
                ';

                if (! in_array($module, $modules, true)) {
                    $modules[] = $module;
                }
            }
        }

        return $output;
    }
}
