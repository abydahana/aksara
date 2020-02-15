<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Groups Privileges
 * This module used to manage the group role.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Privileges extends Aksara
{
	private $_table									= 'app__groups_privileges';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('adjust_group_privileges'))
		->set_icon('mdi mdi-account-check-outline')
		->set_description
		('
			' . phrase('you_may_review') . '
			&nbsp;
			<a href="' . go_to('../', array('per_page' => null)) . '" class="badge badge-secondary --xhr">
				<i class="mdi mdi-arrow-right"></i>
				' . phrase('group_privileges') . '
			</a>
			&nbsp;
			' . phrase('after_modify_this_adjustment_to_sets_new_roles') . '
		')
		->set_primary('id')
		->unset_column('id, privileges')
		->unset_view('id')
		->unset_field('id, last_generated')
		->set_field('privileges', 'custom_format', $this->_privileges())
		->set_field('last_generated', 'current_timestamp')
		->set_validation
		(
			array
			(
				'module_alias'						=> 'required',
				'module_slug'						=> 'required'
			)
		)
		->order_by('module')
		->set_alias
		(
			array
			(
				'module'							=> phrase('module'),
				'submodule'							=> phrase('submodule'),
				'controller'						=> phrase('controller'),
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
		$query										= $this->model->select('privileges')->limit(1)->get_where($this->_table, array('id' => $this->input->get('id')))->row('privileges');
		$privileges									= json_decode($query, true);
		if(is_array($privileges) && sizeof($privileges) > 0 && $this->_method != 'create')
		{
			foreach($privileges as $key => $val)
			{
				$output								.= '
					<div class="col-md-6 privilege mt-1">
						' . (in_array($this->_method, array('create', 'update')) ? '
							<div class="input-group input-group-sm">
								<input type="text" name="privileges[]" class="form-control input-sm bordered" placeholder="' . phrase('example') . ': create" value="' . $val . '" />
								<div class="input-group-append">
									<a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
										<i class="mdi mdi-window-close"></i>
									</a>
								</div>
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
						<input type="text" name="privileges[]" class="form-control input-sm bordered" placeholder="' . phrase('example') . ': create" value="index" />
						<div class="input-group-append">
							<a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
								<i class="mdi mdi-window-close"></i>
							</a>
						</div>
					</div>
				</div>
			';
		}
		$output										= '
			<div class="row">
				<div class="col-md-6 privilege mt-1" style="display:none">
					<div class="input-group input-group-sm">
						<input type="text" name="" class="form-control input-sm bordered" placeholder="' . phrase('example') . ': create" value="index" />
						<div class="input-group-append">
							<a href="javascript:void(0)" class="btn btn-secondary" onclick="jExec(this.closest(\'.privilege\').remove())">
								<i class="mdi mdi-window-close"></i>
							</a>
						</div>
					</div>
				</div>
			
				' . $output . '
				
				' . (in_array($this->_method, array('index', 'create', 'update')) ? '
				<div class="col-md-6 mt-1">
					<a href="javascript:void(0)" class="btn btn-secondary btn-sm btn-block" onclick="jExec($(this).closest(\'.row\').find(\'.privilege\').first().clone().insertBefore($(this).closest(\'.col-md-6\')).css(\'display\', \'block\').find(\'input[type=text]\').attr(\'name\', \'privileges[]\').val(\'\'))">
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