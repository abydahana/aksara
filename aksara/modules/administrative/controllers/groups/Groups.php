<?php namespace Aksara\Modules\Administrative\Controllers\Groups;
/**
 * Administrative > Groups
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Groups extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__groups';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->unset_delete('group_id', array(1, 2, 3));
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_groups'))
		->set_icon('mdi mdi-key')
		->set_description
		('
			<div class="alert-info pr-3 pl-3" style="margin-left:-15px; margin-right:-15px">
				' . phrase('the_privileges_listed_below_is_created_automatically_from') . '
				&nbsp;
				<a href="' . go_to('privileges', array('per_page' => null)) . '" class="badge badge-secondary --xhr">
					<i class="mdi mdi-arrow-right"></i>
					' . phrase('privileges_adjustment') . '
				</a>
			</div>
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
		$modules									= $this->model->select
		('
			module,
			submodule,
			controller,
			privileges
		')
		->order_by('module, (module != submodule), submodule')
		->get('app__groups_privileges')
		->result();
		
		$current									= $this->model->select('group_privileges')->get_where($this->_table, array('group_id' => service('request')->getGet('group_id')), 1)->row('group_privileges');
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
						$label						= phrase($privilege);
						$badge_color				= 'badge-primary';
					}
					elseif('create' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-success';
					}
					elseif('read' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-info';
					}
					elseif('update' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-warning';
					}
					elseif('delete' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-danger';
					}
					elseif('export' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-success';
					}
					elseif('print' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-warning';
					}
					elseif('pdf' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-danger';
					}
					else
					{
						$label						= phrase($privilege);
						$badge_color				= 'badge-secondary';
					}
					
					if('read' === $this->_method)
					{
						if(isset($current[$val->module][$val->submodule][$val->controller]) && in_array($privilege, $current[$val->module][$val->submodule][$val->controller]))
						{
							$privilege_output		.= '
								<div class="col-6 col-md-3">
									<label class="d-block font-weight-bold text-truncate"' . (strlen($label) > 12 ? ' data-toggle="tooltip" title="' . $label . '"' : null) . '>
										<span class="badge ' . $badge_color . '">' . $label . '</span>
									</label>
								</div>
							';
						}
					}
					else
					{
						$privilege_output			.= '
							<div class="col-6 col-md-3">
								<label class="d-block font-weight-bold"' . (strlen($label) > 12 ? ' data-toggle="tooltip" title="' . $label . '"' : null) . '>
									<input type="checkbox" name="group_privileges[' . $val->module . '][' . $val->submodule . '][' . $val->controller . '][]" value="' . $privilege . '" class="checker-children"' . (isset($current[$val->module][$val->submodule][$val->controller]) && in_array($privilege, $current[$val->module][$val->submodule][$val->controller]) ? ' checked' : '') . ' />
									&nbsp;
									<span class="badge ' . $badge_color . '">' . $label . '</span>
								</label>
							</div>
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
