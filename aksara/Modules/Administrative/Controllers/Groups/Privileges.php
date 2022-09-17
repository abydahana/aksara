<?php

namespace Aksara\Modules\Administrative\Controllers\Groups;

/**
 * Administrative > Groups > Privileges
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Privileges extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__groups_privileges';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_primary('path');
	}
	
	public function index()
	{
		$this->set_title(phrase('adjust_group_privileges'))
		->set_icon('mdi mdi-account-check-outline')
		->set_description
		('
			<div class="row">
				<div class="col-12">
					' . phrase('you_may_review') . '
					&nbsp;
					<a href="' . go_to('../', array('per_page' => null)) . '" class="badge bg-secondary --xhr">
						<i class="mdi mdi-arrow-right"></i>
						' . phrase('group_privileges') . '
					</a>
					&nbsp;
					' . phrase('after_modify_this_adjustment_to_sets_new_roles') . '
				</div>
			</div>
		')
		->unset_column('id, privileges')
		->unset_view('id')
		->unset_field('id, last_generated')
		->set_field('privileges', 'custom_format', $this->_privileges())
		->set_field('last_generated', 'current_timestamp')
		->order_by('path')
		->set_validation
		(
			array
			(
				'path'								=> 'required|unique[' . $this->_table . '.path.path.' . service('request')->getGet('path') . ']',
				'privileges'						=> 'required'
			)
		)
		->set_alias
		(
			array
			(
				'path'								=> phrase('path'),
				'privileges'						=> phrase('privileges'),
				'last_generated'					=> phrase('last_generated')
			)
		)
		->render($this->_table);
	}
	
	private function _privileges()
	{
		$query										= null;
		$output										= null;
		$privileges									= array();
		
		$query										= $this->model->select
		('
			privileges
		')
		->get_where
		(
			$this->_table,
			array
			(
				'path'								=> service('request')->getGet('path')
			),
			1
		)
		->row('privileges');
		
		$privileges									= ($query ? json_decode($query, true) : array());
		
		if(is_array($privileges) && sizeof($privileges) > 0 && $this->_method != 'create')
		{
			foreach($privileges as $key => $val)
			{
				$output								.= '
					<div class="col-md-6 privilege mt-1">
						' . (in_array($this->_method, array('create', 'update')) ? '
							<div class="input-group">
								<input type="text" name="privileges[]" class="form-control form-control-sm bordered" placeholder="' . phrase('example') . ': create" value="' . $val . '" />
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
		}
		else
		{
			$output									.= '
				<div class="col-md-6 privilege mt-1">
					<div class="input-group input-group-sm">
						<input type="text" name="privileges[]" class="form-control" placeholder="' . phrase('example') . ': create" value="index" />
						<a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
							<i class="mdi mdi-window-close"></i>
						</a>
					</div>
				</div>
			';
		}
		$output										= '
			<div class="row">
				<div class="col-md-6 privilege mt-1" style="display:none">
					<div class="input-group input-group-sm">
						<input type="text" name="" class="form-control bordered" placeholder="' . phrase('example') . ': create" value="index" />
						<a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
							<i class="mdi mdi-window-close"></i>
						</a>
					</div>
				</div>
			
				' . $output . '
				
				' . (in_array($this->_method, array('index', 'create', 'update')) ? '
				<div class="col-md-6 mt-1">
					<a href="javascript:void(0)" class="btn btn-secondary btn-sm d-block" onclick="jExec($(this).closest(\'.row\').find(\'.privilege\').first().clone().insertBefore($(this).closest(\'.col-md-6\')).css(\'display\', \'block\').find(\'input[type=text]\').attr(\'name\', \'privileges[]\').val(\'\'))">
						<i class="mdi mdi-plus"></i>
						&nbsp;
						' . phrase('add_method') . '
					</a>
				</div>
				' : '') . '
			</div>
		';
		
		return $output;
	}
}
