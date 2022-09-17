<?php

namespace Aksara\Modules\Administrative\Controllers\Menus;

/**
 * Administrative > Menus
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Menus extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__menus';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_delete('menu_id', array(1));
	}
	
	public function index()
	{
		$this->set_title(phrase('menu_management'))
		->set_icon('mdi mdi-menu')
		->unset_column('menu_id, serialized_data')
		->unset_view('menu_id')
		->column_order('menu_label, menu_placement, menu_description, group_name')
		->unset_field('menu_id')
		->set_primary('menu_id')
		->set_field('serialized_data', 'custom_format', $this->_menus())
		->set_field
		(
			'menu_placement',
			'dropdown',
			array
			(
				'header'							=> phrase('header'),
				'sidebar'							=> phrase('sidebar')
			)
		)
		->set_field('status', 'boolean')
		->set_relation
		(
			'group_id',
			'app__groups.group_id',
			'{app__groups.group_name}',
			array
			(
				'app__groups.status'				=> 1
			)
		)
		->set_alias
		(
			array
			(
				'menu_placement'					=> phrase('placement'),
				'menu_label'						=> phrase('menu_label'),
				'menu_description'					=> phrase('description'),
				'serialized_data'					=> phrase('menus'),
				'group_id'							=> phrase('group'),
				'group_name'						=> phrase('group'),
				'status'							=> phrase('status')
			)
		)
		
		->set_validation
		(
			array
			(
				'menu_label'						=> 'required',
				'menu_placement'					=> 'required|in_list[header,sidebar]',
				'status'							=> 'boolean'
			)
		)
		
		->modal_size('modal-lg')
		
		->order_by('menu_id')
		
		->render($this->_table);
	}
	
	private function _menus()
	{
		$output										= null;
		$menus										= null;
		$serialized									= $this->model->select
		('
			serialized_data
		')
		->get_where
		(
			$this->_table,
			array
			(
				'menu_id'							=> service('request')->getGet('menu_id')
			),
			1
		)
		->row('serialized_data');
		
		$serialized_menus							= ($serialized ? json_decode($serialized) : new \stdClass());
		
		if($serialized_menus)
		{
			foreach($serialized_menus as $key => $val)
			{
				if(!isset($val->id) || !isset($val->label) || !isset($val->slug)) continue;
				$menus								.= '
					<li id="' . $key . '" class="mt-2" data-icon="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '" data-label="' . $val->label . '" data-slug="' . $val->slug . '" data-newtab="' . (isset($val->newtab) ? $val->newtab : 0) . '">
						<div class="input-group input-group-sm">
							<button class="btn btn-secondary" type="button">
								<i class="mdi mdi-reorder-horizontal"></i>
							</button>
							<button class="btn btn-secondary menu-icon ignore-sort" type="button" role="iconpicker" data-iconset="materialdesign" data-icon="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '"></button>
							<input type="text" class="form-control menu-label ignore-sort" placeholder="' . phrase('menu_label') . '" value="' . $val->label . '" />
							<input type="text" class="form-control menu-slug ignore-sort" placeholder="' . phrase('menu_slug') . '" value="' . $val->slug . '" />
							<div class="input-group-text bg-secondary border-0 ignore-sort pt-0" data-bs-toggle="tooltip" title="' . phrase('open_in_new_tab') . '">
								<input type="checkbox" class="form-check-input menu-newtab"' . (isset($val->newtab) && 1 == $val->newtab ? ' checked' : null) . ' />
							</div>
							<button type="button" class="btn btn-secondary item-add children ignore-sort">
								<i class="mdi mdi-plus"></i>
							</button>
							<button type="button" class="btn btn-secondary item-remove ignore-sort">
								<i class="mdi mdi-window-close"></i>
							</button>
						</div>
						' . (isset($val->children) && is_array($val->children) && sizeof($val->children) > 0 ? $this->_children_check($val->children, $key) : null) . '
					</li>
				';
			}
		}
		
		$output										= '
			<div class="mb-3" id="serialized_data_input">
				<ul class="list sortable" role="sortable-menu">
					<li class="ignore-sort">
						<div class="d-grid">
							<button type="button" class="btn btn-default btn-sm item-add masking">
								<i class="mdi mdi-plus mdi-2x"></i>
							</button>
						</div>
					</li>
					<li id="{{id}}" class="mt-2 item-placeholder hidden" data-icon="mdi mdi-radiobox-blank" data-label="' . phrase('menu_label') . '" data-slug="home" data-newtab="0">
						<div class="input-group input-group-sm">
							<button class="btn btn-secondary" type="button">
								<i class="mdi mdi-reorder-horizontal"></i>
							</button>
							<button class="btn btn-secondary menu-icon ignore-sort" type="button" role="iconpicker" data-iconset="materialdesign" data-icon="mdi mdi-radiobox-blank"></button>
							<input type="text" class="form-control menu-label ignore-sort" placeholder="' . phrase('menu_label') . '" value="' . phrase('menu_label') . '" />
							<input type="text" class="form-control menu-slug ignore-sort" placeholder="' . phrase('menu_slug') . '" value="" />
							<div class="input-group-text bg-secondary border-0 ignore-sort pt-0" data-bs-toggle="tooltip" title="' . phrase('open_in_new_tab') . '">
								<input type="checkbox" class="form-check-input menu-newtab" />
							</div>
							<button type="button" class="btn btn-secondary item-add children ignore-sort">
								<i class="mdi mdi-plus"></i>
							</button>
							<button type="button" class="btn btn-secondary item-remove ignore-sort">
								<i class="mdi mdi-window-close"></i>
							</button>
						</div>
					</li>
					' . $menus . '
				</ul>
				<input type="hidden" name="serialized_data" value="' . ($serialized ? htmlspecialchars($serialized) : '[]') . '" class="serialized_data" />
			</div>
		';
		
		if('read' != $this->_method)
		{
			return $output;
		}
		else
		{
			$output									= null;
			$menus									= null;
			if($serialized_menus)
			{
				foreach($serialized_menus as $key => $val)
				{
					if(!isset($val->id) || !isset($val->label) || !isset($val->slug)) continue;
					$menus							.= '
						<li>
							<a href="' . base_url($val->slug) . '" target="_blank">
								<i class="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '"></i>
								&nbsp;
								' . $val->label . '
							</a>
							' . (isset($val->children) && is_array($val->children) && sizeof($val->children) > 0 ? $this->_children_tree($val->children) : null) . '
						</li>
					';
				}
				$output								= '
					<ul class="list-unstyled">
						' . $menus . '
					</ul>
				';
			}
			return $output;
		}
	}
	
	private function _children_check($data = array(), $id = null)
	{
		$output										= null;
		$menus										= null;
		if($data)
		{
			foreach($data as $key => $val)
			{
				$menus								.= '
					<li id="' . $id . '" class="mt-2" data-icon="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '" data-label="' . $val->label . '" data-slug="' . $val->slug . '" data-newtab="' . (isset($val->newtab) ? $val->newtab : 0) . '">
						<div class="input-group input-group-sm">
							<button class="btn btn-secondary" type="button">
								<i class="mdi mdi-reorder-horizontal"></i>
							</button>
							<button class="btn btn-secondary menu-icon ignore-sort" type="button" role="iconpicker" data-iconset="materialdesign" data-icon="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '"></button>
							<input type="text" class="form-control menu-label ignore-sort" placeholder="' . phrase('menu_label') . '" value="' . $val->label . '" />
							<input type="text" class="form-control menu-slug ignore-sort" placeholder="' . phrase('menu_slug') . '" value="' . $val->slug . '" />
							<div class="input-group-text bg-secondary border-0 ignore-sort pt-0" data-bs-toggle="tooltip" title="' . phrase('open_in_new_tab') . '">
								<input type="checkbox" class="form-check-input menu-newtab"' . (isset($val->newtab) && 1 == $val->newtab ? ' checked' : null) . ' />
							</div>
							<button type="button" class="btn btn-secondary item-add children ignore-sort">
								<i class="mdi mdi-plus"></i>
							</button>
							<button type="button" class="btn btn-secondary item-remove ignore-sort">
								<i class="mdi mdi-window-close"></i>
							</button>
						</div>
						' . (isset($val->children) && is_array($val->children) && sizeof($val->children) > 0 ? $this->_children_check($val->children, $key) : null) . '
					</li>
				';
			}
		}
		if($menus)
		{
			$output									= '
				<ul>
					' . $menus . '
				</ul>
			';
		}
		return $output;
	}
	
	private function _children_tree($data = array())
	{
		$output										= null;
		$menus										= null;
		if($data)
		{
			foreach($data as $key => $val)
			{
				$menus								.= '
					<li>
						<a href="' . base_url($val->slug) . '" target="_blank">
							<i class="' . ($val->icon ? $val->icon : 'mdi mdi-radiobox-blank') . '"></i>
							&nbsp;
							' . $val->label . '
						</a>
						' . (isset($val->children) && is_array($val->children) && sizeof($val->children) > 0 ? $this->_children_tree($val->children) : null) . '
					</li>
				';
			}
		}
		if($menus)
		{
			$output									= '
				<ul class="list-unstyled ms-4">
					' . $menus . '
				</ul>
			';
		}
		return $output;
	}
}
