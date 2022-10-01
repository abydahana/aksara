<?php

namespace Aksara\Modules\Administrative\Controllers\Groups;

/**
 * Administrative > Groups
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_delete('group_id', array(1, 2, 3));
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_groups'))
		->set_icon('mdi mdi-key')
		->set_description
		('
			<div class="row">
				<div class="col-12">
					' . phrase('the_listed_privileges_below_is_created_automatically_from') . '
					&nbsp;
					<a href="' . go_to('privileges', array('per_page' => null)) . '" class="badge bg-secondary --xhr">
						<i class="mdi mdi-arrow-right"></i>
						' . phrase('privileges_adjustment') . '
					</a>
				</div>
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
		
		->modal_size('modal-lg')
		
		->order_by('group_id')
		
		->render($this->_table);
	}
	
	private function _privileges()
	{
		if(!in_array($this->_method, array('create', 'read', 'update'))) return false;
		
		$modules_collection							= $this->model->select
		('
			path,
			privileges
		')
		->order_by('path')
		->get('app__groups_privileges')
		->result();
		
		$current									= $this->model->select
		('
			group_privileges
		')
		->get_where
		(
			$this->_table,
			array
			(
				'group_id'							=> service('request')->getGet('group_id')
			),
			1
		)
		->row('group_privileges');
		
		$current									= ($current ? json_decode($current, true) : array());
		$output										= null;
		
		if($modules_collection)
		{
			$prepare								= array();
			$modules								= array();
			
			foreach($modules_collection as $key => $val)
			{
				$path								= str_replace('/', '__', $val->path);
				$privilege_output					= null;
				$privileges							= ($val->privileges ? json_decode($val->privileges) : new \stdClass());
				
				if(!$privileges) continue;
				
				foreach($privileges as $key => $privilege)
				{
					if('index' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-primary';
					}
					else if('create' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-success';
					}
					else if('read' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-info';
					}
					else if('update' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-warning';
					}
					else if('delete' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-danger';
					}
					else if('export' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-success';
					}
					else if('print' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-warning';
					}
					else if('pdf' == $privilege)
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-danger';
					}
					else
					{
						$label						= phrase($privilege);
						$badge_color				= 'bg-secondary';
					}
					
					if('read' === $this->_method)
					{
						if(isset($current[$val->path]) && in_array($privilege, $current[$val->path]))
						{
							$privilege_output		.= '
								<div class="col-6 col-md-3">
									<label class="d-block mb-0"' . (strlen($label) > 12 ? ' data-bs-toggle="tooltip" title="' . $label . '"' : null) . '>
										<span class="badge text-dark pe-0 ps-0"><i class="mdi mdi-check text-primary"></i> ' . $label . '</span>
									</label>
								</div>
							';
						}
					}
					else
					{
						$privilege_output			.= '
							<div class="col-6 col-md-3 bg-white">
								<div class="form-check form-switch mb-0"' . (strlen($label) > 12 ? ' data-bs-toggle="tooltip" title="' . $label . '"' : null) . '>
									<input type="checkbox" name="group_privileges[' . $val->path . '][]" value="' . $privilege . '" class="form-check-input checker-children" id="' . $path . '_' . $privilege . '"' . (isset($current[$val->path]) && in_array($privilege, $current[$val->path]) ? ' checked' : '') . ' />
									<label class="form-check-label text-sm" for="' . $path . '_' . $privilege . '">
										' . $label . '
									</label>
								</div>
							</div>
						';
					}
				}
				
				$paths								= explode('/', $val->path);
				list($module)						= array_pad($paths, 1, null);
				$module_path						= null;
				
				foreach($paths as $_key => $_val)
				{
					$module_path					.= ($_key ? ' &gt; ' : null) . phrase($_val);
				}
				
				$output								.= '
					' . (!in_array($module, $modules) ? '
					' . ($modules ? '<hr />' : null) . '
					<a href="' . base_url($val->path) . '" target="_blank">
						<b>
							<i class="mdi mdi-puzzle-outline"></i>
							&nbsp;
							' . phrase('module') . ': ' . phrase($module) . '
						</b>
					</a>
					' : '') . '
					<div class="check-group">
						<div class="form-check form-switch">
							' . (in_array($this->_method, array('create', 'update')) ? '<input type="checkbox" class="form-check-input" id="' . $path . '" data-bs-toggle="tooltip" title="' . phrase('check_all') . '" role="checker" data-parent=".check-group" />' : '<input type="checkbox" class="form-check-input" checked disabled />') . '
							<label class="form-check-label fw-bold" for="' . $path . '">
								' . $module_path . '
							</label>
							<a href="' . base_url($val->path) . '" target="_blank">
								<i class="mdi mdi-launch"></i>
							</a>
						</div>
						<div class="row mb-3">
							' . $privilege_output . '
						</div>
					</div>
				';
				
				if(!in_array($module, $modules))
				{
					$modules[]						= $module;
				}
			}
		}
		
		return $output;
	}
}
