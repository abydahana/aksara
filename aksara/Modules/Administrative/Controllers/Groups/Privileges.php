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

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->set_primary('path');
    }

    public function index()
    {
        $this->set_title(phrase('Adjust Group Privileges'))
        ->set_icon('mdi mdi-account-check-outline')
        ->set_description('
            <div class="row">
                <div class="col-12">
                    ' . phrase('You may review {{group_privileges}} after modifying this adjustment to sets new roles.', ['group_privileges' => '<a href="' . go_to('../', ['q' => null, 'per_page' => null, 'group_id' => null, 'path' => null]) . '" class="badge bg-secondary --xhr"><i class="mdi mdi-arrow-right"></i> ' . phrase('Group Privileges') . '</a>']) . '
                </div>
            </div>
        ')
        ->unset_column('id, privileges')
        ->unset_view('id')
        ->unset_field('id, last_generated')
        ->set_field('privileges', 'custom_format', $this->_privileges())
        ->set_field('last_generated', 'current_timestamp')
        ->order_by('path')
        ->set_validation([
            'path' => 'required|unique[' . $this->_table . '.path.path.' . $this->request->getGet('path') . ']',
            'privileges' => 'required'
        ])
        ->set_alias([
            'path' => phrase('Path'),
            'privileges' => phrase('Privileges'),
            'last_generated' => phrase('Last Generated')
        ])
        ->render($this->_table);
    }

    private function _privileges()
    {
        $query = null;
        $output = null;
        $privileges = [];

        $query = $this->model->select('
            privileges
        ')
        ->get_where(
            $this->_table,
            [
                'path' => $this->request->getGet('path')
            ],
            1
        )
        ->row('privileges');

        $privileges = ($query ? json_decode($query, true) : []);

        if (is_array($privileges) && sizeof($privileges) > 0 && $this->get_method() != 'create') {
            foreach ($privileges as $key => $val) {
                $output .= '
                    <div class="col-md-6 privilege mt-1">
                        ' . (in_array($this->get_method(), ['create', 'update']) ? '
                            <div class="input-group">
                                <input type="text" name="privileges[]" class="form-control form-control-sm bordered" placeholder="' . phrase('Example') . ': create" value="' . $val . '" />
                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="jExec(this.closest(\'.privilege\').remove())">
                                    <i class="mdi mdi-window-close"></i>
                                </a>
                            </div>
                        ' : '
                            <label class="control-label">
                                <i class="mdi mdi-check"></i>
                                ' . phrase($val) . '
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

                    ' . (in_array($this->get_method(), ['index', 'create', 'update']) ? '
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
