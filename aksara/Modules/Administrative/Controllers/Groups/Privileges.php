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

class Privileges extends Core
{
    private $_table = 'app__groups_privileges';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->setPrimary('path');
    }

    public function index()
    {
        $this->setTitle(phrase('Adjust Group Privileges'))
        ->setIcon('mdi mdi-account-check-outline')
        ->setDescription('
            <div class="row">
                <div class="col-12">
                    ' . phrase('You may review {{group_privileges}} after modifying this adjustment to sets new roles.', ['group_privileges' => '<a href="' . go_to('../', ['q' => null, 'per_page' => null, 'group_id' => null, 'path' => null]) . '" class="badge bg-secondary --xhr"><i class="mdi mdi-arrow-right"></i> ' . phrase('Group Privileges') . '</a>']) . '
                </div>
            </div>
        ')
        ->unsetColumn('id, privileges')
        ->unsetView('id')
        ->unsetField('id, last_generated')
        ->setField('privileges', 'custom_format', 'formatPrivileges')
        ->setField('last_generated', 'current_timestamp')
        ->orderBy('path')
        ->setValidation([
            'path' => 'required|unique[' . $this->_table . '.path.path.' . $this->request->getGet('path') . ']',
            'privileges' => 'required'
        ])
        ->setAlias([
            'path' => phrase('Path'),
            'privileges' => phrase('Privileges'),
            'last_generated' => phrase('Last Generated')
        ])
        ->render($this->_table);
    }

    protected function formatPrivileges(array $data)
    {
        $output = null;
        $privileges = [];

        $privileges = ($data['privileges'] ? json_decode($data['privileges'], true) : []);

        if (is_array($privileges) && sizeof($privileges) > 0 && $this->getMethod() != 'create') {
            foreach ($privileges as $key => $val) {
                $output .= '
                    <div class="col-md-6 privilege mt-1">
                        ' . (in_array($this->getMethod(), ['create', 'update'], true) ? '
                            <div class="input-group">
                                <input type="text" name="privileges[]" class="form-control form-control-sm bordered" placeholder="' . phrase('Example') . ': create" value="' . $val . '" />
                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="jExec(this.closest(\'.privilege\').remove())">
                                    <i class="mdi mdi-window-close"></i>
                                </a>
                            </div>
                        ' : '
                            <label class="control-label">
                                <i class="mdi mdi-check"></i>
                                ' . phrase($val, [], true) . '
                            </label>
                        ') . '
                    </div>
                ';
            }
        } else {
            $output .= '
                <div class="col-md-6 privilege mt-1">
                    <div class="input-group input-group-sm">
                        <input type="text" name="privileges[]" class="form-control" placeholder="' . phrase('Example') . ': create" value="index" />
                        <a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
                            <i class="mdi mdi-window-close"></i>
                        </a>
                    </div>
                </div>
            ';
        }
        $output = '
            <div class="w-100">
                <div class="row">
                    <div class="col-md-6 privilege mt-1" style="display:none">
                        <div class="input-group input-group-sm">
                            <input type="text" name="" class="form-control bordered" placeholder="' . phrase('Example') . ': create" value="index" />
                            <a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
                                <i class="mdi mdi-window-close"></i>
                            </a>
                        </div>
                    </div>

                    ' . $output . '

                    ' . (in_array($this->getMethod(), ['index', 'create', 'update'], true) ? '
                    <div class="col-md-6 mt-1">
                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm d-block" onclick="jExec($(this).closest(\'.row\').find(\'.privilege\').first().clone().insertBefore($(this).closest(\'.col-md-6\')).css(\'display\', \'block\').find(\'input[type=text]\').attr(\'name\', \'privileges[]\').val(\'\'))">
                            <i class="mdi mdi-plus"></i>
                            &nbsp;
                            ' . phrase('Add Method') . '
                        </a>
                    </div>
                    ' : '') . '
                </div>
            </div>
        ';

        return $output;
    }
}
