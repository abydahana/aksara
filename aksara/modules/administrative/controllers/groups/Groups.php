<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Groups
 * This module used to manage the group user.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Groups extends Aksara
{
	private $_table									= 'app__groups';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		$this->unset_delete('group_id', array('1')); // prevent user to delete group id 1
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_groups'))
		->set_icon('mdi mdi-key')
		->set_description
		('
			' . phrase('the_privileges_listed_below_is_created_automatically_from') . '
			&nbsp;
			<a href="' . go_to('privileges', array('per_page' => null)) . '" class="badge badge-secondary --xhr">
				<i class="mdi mdi-arrow-right"></i>
				' . phrase('privileges_adjustment') . '
			</a>
		')
		->set_primary('group_id')
		->unset_column('group_id, group_privileges')
		->unset_view('group_id')
		->unset_field('group_id')
		->set_field
		(
			array
			(
				'group_description'					=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->set_field('group_privileges', 'custom_format', $this->_privileges())
		->set_validation
		(
			array
			(
				'group_name'						=> 'required',
				'group_description'					=> 'required'
			)
		)
		->set_alias
		(
			array
			(
				'group_name'						=> phrase('group_name'),
				'group_description'					=> phrase('description'),
				'group_privileges'					=> phrase('privileges'),
				'status'							=> phrase('status')
			)
		)
		->render($this->_table);
	}
	
	private function _privileges()
	{
		$modules									= $this->model->select('module, submodule, controller, privileges')->get('app__groups_privileges')->result();
		$current									= $this->model->select('group_privileges')->get_where($this->_table, array('group_id' => $this->input->get('group_id')), 1)->row('group_privileges');
		$current									= json_decode($current, true);
		$output										= null;
		if($modules)
		{
			$prepare								= array();
			foreach($modules as $key => $val)
			{
				if($val->module == $val->submodule && $val->submodule == $val->controller)
				{
					$val->submodule					= null;
					$val->controller				= null;
				}
				elseif($val->submodule == $val->controller)
				{
					$val->controller				= null;
				}
				$prepare[]							= $val;
			}
			
			$module									= null;
			$modules								= array_sort($prepare, array('module' => 'asc', 'submodule' => 'asc', 'controller' => 'asc'));
			
			foreach($modules as $key => $val)
			{
				if(!$val->submodule)
				{
					$val->submodule					= $val->module;
				}
				if(!$val->controller)
				{
					$val->controller				= $val->submodule;
				}
				$privilege_output					= null;
				$privileges							= json_decode($val->privileges);
				if(!$privileges) continue;
				foreach($privileges as $key => $privilege)
				{
					if('index' == $privilege)
					{
						$label						= '<span class="badge badge-primary w-75">' . phrase($privilege) . '</span>';
					}
					elseif('create' == $privilege)
					{
						$label						= '<span class="badge badge-success w-75">' . phrase($privilege) . '</span>';
					}
					elseif('read' == $privilege)
					{
						$label						= '<span class="badge badge-info w-75">' . phrase($privilege) . '</span>';
					}
					elseif('update' == $privilege)
					{
						$label						= '<span class="badge badge-warning w-75">' . phrase($privilege) . '</span>';
					}
					elseif('delete' == $privilege)
					{
						$label						= '<span class="badge badge-danger w-75">' . phrase($privilege) . '</span>';
					}
					elseif('export' == $privilege)
					{
						$label						= '<span class="badge badge-success w-75">' . phrase($privilege) . '</span>';
					}
					elseif('print' == $privilege)
					{
						$label						= '<span class="badge badge-warning w-75">' . phrase($privilege) . '</span>';
					}
					elseif('pdf' == $privilege)
					{
						$label						= '<span class="badge badge-danger w-75">' . phrase($privilege) . '</span>';
					}
					else
					{
						$label						= '<span class="badge badge-secondary w-75 rounded-right"' . (strlen($privilege) > 16 ? ' data-toggle="tooltip" title="' . phrase($privilege) . '"' : null) . '>' . (strlen($privilege) > 16 ? truncate(phrase($privilege), 16) : phrase($privilege)) . '</span>';
					}
					if('read' === $this->_method)
					{
						if(isset($current[$val->module][$val->submodule][$val->controller]) && in_array($privilege, $current[$val->module][$val->submodule][$val->controller]))
						{
							$privilege_output		.= '
								<label class="col-6 col-md-3">
									' . $label . '
								</label>
							';
						}
					}
					else
					{
						$privilege_output			.= '
							<label class="col-6 col-md-3 d-block text-truncate">
								<input type="checkbox" name="group_privileges[' . $val->module . '][' . $val->submodule . '][' . $val->controller . '][]" value="' . $privilege . '" class="checker-children"' . (isset($current[$val->module][$val->submodule][$val->controller]) && in_array($privilege, $current[$val->module][$val->submodule][$val->controller]) ? ' checked' : '') . ' />
								' . $label . '
							</label>
						';
					}
				}
				$output								.= '
					' . ($module != $val->module ? '
					<a href="' . base_url($val->module) . '" target="_blank">
						<label class="d-block font-weight-bold">
							<i class="mdi mdi-puzzle-outline"></i>
							&nbsp;
							' . phrase('module') . ': ' . phrase($val->module) . '
						</label>
					</a>
					' : '') . '
					<div class="check-group text-truncate">
						<label class="d-block">
							' . (in_array($this->_method, array('create', 'update')) ? '<input type="checkbox" data-toggle="tooltip" title="' . phrase('check_all') . '" role="checker" data-parent=".check-group" />
							&nbsp;' : null) . '
							' . phrase($val->controller) . '
							<a href="' . base_url($val->module . ($val->module != $val->submodule ? '/' . $val->submodule : null) . ($val->submodule != $val->controller ? '/' . $val->controller : null)) . '" target="_blank">
								<small class="text-muted">
									(' . $val->module . ($val->module != $val->submodule ? '/' . $val->submodule : null) . ($val->submodule != $val->controller ? '/' . $val->controller : null) . ')
									<i class="mdi mdi-link"></i>
								</small>
							</a>
						</label>
						<div class="row form-group">
							' . $privilege_output . '
						</div>
					</div>
				';
				$module								= $val->module;
			}
		}
		return $output;
	}
}