<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Menu Helper
 *
 * This helper manage a created menu from database
 *
 * @package		Menu
 * @version		4.2.1
 * @author 		Aby Dahana <abydahana@gmail.com>
 * @copyright 	Copyright (c) 2016, Aby Dahana
 * @link		https://www.facebook.com/abyprogrammer
**/

if(!function_exists('generate_menu'))
{
	function generate_menu($alias = null, $placement = null, $additional_class = null)
	{
		$CI											=& get_instance();
		$parent										= $CI->router->fetch_module();
		$module										= $CI->router->fetch_class();
		$method										= $CI->router->fetch_method();
		$initial									= $CI->uri->segment_array();
		$menus										= null;
		$user_id									= get_userdata('user_id');
		$access_year								= get_userdata('year');
		$group_id									= get_userdata('group_id');
		
		if(get_userdata('is_logged'))
		{
			$menu									= $CI->db->select
			('
				serialized_data
			')
			->get_where
			(
				'app__menus',
				array
				(
					'menu_placement'				=> $alias,
					'group_id'						=> $group_id
				),
				1
			)
			->row('serialized_data');
			
			$visible_menus							= $CI->db->select
			('
				visible_menu
			')
			->get_where
			(
				'app__users_privileges',
				array
				(
					'user_id'						=> $user_id
				),
				1
			)
			->row('visible_menu');
			
			$visible_menus							= json_decode($visible_menus, true);
			
			if(!$menu)
			{
				$menu								= $CI->db->select
				('
					serialized_data
				')
				->get_where
				(
					'app__menus',
					array
					(
						'menu_placement'			=> $alias,
						'group_id'					=> 0
					), 
					1
				)
				->row('serialized_data');
			}
		}
		else
		{
			$visible_menus							= array();
			$menu									= $CI->db->select
			('
				serialized_data
			')
			->get_where
			(
				'app__menus',
				array
				(
					'menu_placement'				=> $alias,
					'group_id'						=> 0
				),
				1
			)
			->row('serialized_data');
		}
		
		$menu										= json_decode($menu, true);
		
		if(!$menu)
		{
			$menu									= array();
		}
		
		$main_menu									= array
		(
			array
			(
				'id'								=> 0,
				'label'								=> '',
				'slug'								=> '---'
			),
			array
			(
				'id'								=> 0,
				'label'								=> 'CMS',
				'slug'								=> 'cms',
				'icon'								=> 'mdi mdi-dropbox',
				'children'							=> array
				(
					array
					(
						'id'						=> 0,
						'label'						=> 'Blogs',
						'slug'						=> 'cms/blogs',
						'icon'						=> 'mdi mdi-newspaper',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Posts',
								'slug'				=> 'cms/blogs',
								'icon'				=> 'mdi mdi-pencil'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Categories',
								'slug'				=> 'cms/blogs/categories',
								'icon'				=> 'mdi mdi-sitemap'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Pages',
						'slug'						=> 'cms/pages',
						'icon'						=> 'mdi mdi-book-open-page-variant'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Galleries',
						'slug'						=> 'cms/galleries',
						'icon'						=> 'mdi mdi-folder-multiple-image'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Peoples',
						'slug'						=> 'cms/peoples',
						'icon'						=> 'mdi mdi-account-group-outline'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Partial Content',
						'slug'						=> 'cms/partials',
						'icon'						=> 'mdi mdi-file-image',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Carousels',
								'slug'				=> 'cms/partials/carousels',
								'icon'				=> 'mdi mdi-image-multiple'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'FAQs',
								'slug'				=> 'cms/partials/faqs',
								'icon'				=> 'mdi mdi-file-question'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Announcements',
								'slug'				=> 'cms/partials/announcements',
								'icon'				=> 'mdi mdi-bullhorn-outline'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Testimonials',
								'slug'				=> 'cms/partials/testimonials',
								'icon'				=> 'mdi mdi-comment-account-outline'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Media',
								'slug'				=> 'cms/partials/media',
								'icon'				=> 'mdi mdi-folder-image'
							)
						)
					)
				)
			),
			array
			(
				'id'								=> 0,
				'label'								=> 'Core Tools',
				'slug'								=> '---'
			),
			array
			(
				'id'								=> 0,
				'label'								=> 'Administrative',
				'slug'								=> 'administrative',
				'icon'								=> 'mdi mdi-cogs',
				'children'							=> array
				(
					array
					(
						'id'						=> 0,
						'label'						=> 'Users and Groups',
						'slug'						=> 'administrative/users',
						'icon'						=> 'mdi mdi-account-group-outline',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Users',
								'slug'				=> 'administrative/users',
								'icon'				=> 'mdi mdi-account-group'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Groups',
								'slug'				=> 'administrative/groups',
								'icon'				=> 'mdi mdi-sitemap'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Privileges',
								'slug'				=> 'administrative/groups/privileges',
								'icon'				=> 'mdi mdi-account-check-outline'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Configurations',
						'slug'						=> 'administrative',
						'icon'						=> 'mdi mdi-wrench-outline',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Site Settings',
								'slug'				=> 'administrative/settings',
								'icon'				=> 'mdi mdi-wrench mdi-flip-h'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Menus',
								'slug'				=> 'administrative/menus',
								'icon'				=> 'mdi mdi-menu'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Translations',
								'slug'				=> 'administrative/translations',
								'icon'				=> 'mdi mdi-translate'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Countries',
								'slug'				=> 'administrative/countries',
								'icon'				=> 'mdi mdi-map-legend'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Activity Logs',
						'slug'						=> 'administrative/activities',
						'icon'						=> 'mdi mdi-information-outline'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Inquiries',
						'slug'						=> 'administrative/inquiries',
						'icon'						=> 'mdi mdi-message-text'
					)
				)
			),
			array
			(
				'id'								=> 0,
				'label'								=> 'APIs',
				'slug'								=> 'apis',
				'icon'								=> 'mdi mdi-code-braces',
				'children'							=> array
				(
					array
					(
						'id'						=> 0,
						'label'						=> 'Services',
						'slug'						=> 'apis/services',
						'icon'						=> 'mdi mdi-link-variant'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Clients',
						'slug'						=> 'apis/clients',
						'icon'						=> 'mdi mdi-account-check-outline'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Permissions',
						'slug'						=> 'apis/permissions',
						'icon'						=> 'mdi mdi-security-network'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Debug Tool',
						'slug'						=> 'apis/debug_tool',
						'icon'						=> 'mdi mdi-android-debug-bridge'
					)
				)
			)
		);
		
		if(1 != get_userdata('group_id') || 'header' == $placement)
		{
			$main_menu								= array();
		}
		
		$menu										= array_merge($menu, $main_menu);
		
		if('header' == $placement)
		{
			$navigation_class						= 'navbar-nav' . ($additional_class ? ' ' . $additional_class : null);
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= 'dropdown';
			$dropdown_list_class					= 'dropdown-menu';
			$toggle_class							= 'dropdown-toggle';
			$toggle_initial							= 'data-toggle="dropdown"';
			$additional_prefix						= null;
		}
		else
		{
			$navigation_class						= 'nav flex-column' . ($additional_class ? ' ' . $additional_class : null);
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= null;
			$dropdown_list_class					= 'list-unstyled flex-column collapse';
			$toggle_class							= null;
			$toggle_initial							= 'data-toggle="expand-collapse"';
			$additional_prefix						= '
				<li class="nav-item">
					<span class="nav-link hide-on-collapse">
						' . phrase('main_navigation') . '
					</span>
				</li>
				<li class="nav-item' . ('dashboard' == $module ? ' active' : '') . '">
					<a href="' . base_url('dashboard') . '" class="nav-link --xhr">
						<i class="mdi mdi-monitor-dashboard"></i>
						<span class="hide-on-collapse">
							' . phrase('dashboard') . '
						</span>
					</a>
				</li>
			';
		}
		
		foreach($menu as $key => $field)
		{
			$children								= false;
			$arrow									= null;
			
			if(isset($field['id']) && isset($field['label']) && isset($field['slug']))
			{
				if('---' == $field['slug'])
				{
					$menus							.= '
						<li class="nav-item">
							<span class="nav-link hide-on-collapse">
								' . ($field['label'] ? phrase($field['label'], true) : null) . '
							</span>
						</li>
					';
				}
				else
				{
					if(1 != get_userdata('group_id') && $visible_menus && !in_array($field['id'], $visible_menus)) continue;
					
					if(isset($field['children']) && is_array($field['children']) && sizeof($field['children']) > 0)
					{
						$children					= true;
					}
					
					$slug							= explode('/', $field['slug']);
					$slug							= end($slug);
				
					if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $field['slug']))
					{
						$field['slug']				= $field['slug'] . '" target="_blank';
					}
					else
					{
						$field['slug']				= base_url($field['slug']);
					}
					
					$menus							.= '
						<li class="' . $navigation_item_class . ($children ? ' ' . $dropdown_link_class : null) . (in_array($slug, $initial) ? ' active' : '') . '">
							<a href="' . ($children ? '#' : $field['slug']) . '" class="' . $navigation_link_class . (stripos($field['icon'], 'mdi-') !== false ? ' nav-padding-left' : null) . (!$children ? ' --xhr' : ' ' . $toggle_class) . '"' . ($children ? ' ' . $toggle_initial : null) . (isset($field['newtab']) && 1 == $field['newtab'] && !$children ? ' target="_blank"' : null) . '>
								' . (stripos($field['icon'], 'mdi-') !== false ? '<i class="' . (isset($field['icon']) ? $field['icon'] : 'mdi mdi-circle-outline') . '"></i>' : null) . '
								' . ($children && 'header' != $placement ? '<i class="mdi mdi-chevron-right float-right"></i>' : null) . '
								<span class="hide-on-collapse">
									' . ($field['label'] ? phrase($field['label'], true) : null) . '
								</span>
							</a>
							' . ($children ? _children_check($field['children'], $placement, $visible_menus) : '') . '
						</li>
					';
				}
			}
		}
		
		$output										= '
			<ul class="' . $navigation_class . '">
			
				' . $additional_prefix . '
				' . $menus . '
				
				' . ('header' != $placement ? '
				<li class="divider"></li>
				<li class="' . $navigation_item_class . '">
					<a href="' . base_url('pages/about') . '" class="' . $navigation_link_class . ' text-sm hide-on-collapse" target="_blank">
						' . phrase('about') . '
					</a>
				</li>
				<li class="' . $navigation_item_class . '">
					<a href="' . base_url('pages/license') . '" class="' . $navigation_link_class . ' text-sm hide-on-collapse" target="_blank">
						' . phrase('license') . '
					</a>
				</li>
				<li class="' . $navigation_item_class . '">
					<a href="javascript:void(0)" class="' . $navigation_link_class . ' disabled text-sm hide-on-collapse">
						AKSARA ' . aksara('built_version') . '
					</a>
				</li>
				' : '') . '
			</ul>
		';
		
		return $output;
	}
}

if(!function_exists('_children_check'))
{
	function _children_check($menu = array(), $placement = null, $visible_menus = array())
	{
		$CI											=& get_instance();
		$parent										= $CI->router->fetch_module();
		$module										= $CI->router->fetch_class();
		$method										= $CI->router->fetch_method();
		$initial									= $CI->uri->segment_array();
		$menus										= null;
		
		if('header' == $placement)
		{
			$navigation_class						= 'navbar-nav';
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= 'dropdown';
			$dropdown_list_class					= 'dropdown-menu';
			$toggle_class							= 'dropdown-toggle';
			$toggle_initial							= 'data-toggle="dropdown"';
		}
		else
		{
			$navigation_class						= 'nav flex-column';
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= null;
			$dropdown_list_class					= 'list-unstyled flex-column collapse';
			$toggle_class							= null;
			$toggle_initial							= 'data-toggle="expand-collapse"';
		}
		
		foreach($menu as $key => $field)
		{
			$children								= false;
			$arrow									= null;
			
			if(isset($field['id']) && isset($field['label']) && isset($field['slug']))
			{
				if(1 != get_userdata('group_id') && $visible_menus && !in_array($field['id'], $visible_menus)) continue;
				
				if(isset($field['children']) && is_array($field['children']) && sizeof($field['children']) > 0)
				{
					$children						= true;
				}
				
				$slug								= explode('/', $field['slug']);
				$start_slug							= $slug[0];
				$end_slug							= end($slug);
				
				if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $field['slug']))
				{
					$field['slug']					= $field['slug'] . '" target="_blank';
				}
				else
				{
					$field['slug']					= base_url($field['slug']);
				}
				
				$menus								.= '
					<li class="' . $navigation_item_class . (in_array($start_slug, $initial) && in_array($end_slug, $initial) ? ' active' : '') . '">
						<a href="' . ($children ? '#'  : $field['slug']) . '" class="' . $navigation_link_class . (stripos($field['icon'], 'mdi-') !== false ? ' nav-padding-left' : null) . (!$children ? ' --xhr' : ' ' . $toggle_class) . '"' . ($children ? ' ' . $toggle_initial : null) . (isset($field['newtab']) && 1 == $field['newtab'] && !$children ? ' target="_blank"' : null) . '>
							' . (stripos($field['icon'], 'mdi-') !== false ? '<i class="' . (isset($field['icon']) ? $field['icon'] : 'mdi mdi-circle-outline') . '"></i>' : null) . '
							' . ($children && 'header' != $placement ? '<i class="mdi mdi-chevron-right float-right"></i>' : null) . '
							<span class="hide-on-collapse">
								' . ($field['label'] ? phrase($field['label'], true) : null) . '
							</span>
						</a>
						' . ($children ? _children_check($field['children'], $placement, null) : '') . '
					</li>
				';
			}
		}
		
		$output										= '
			<ul class="' . $dropdown_list_class . '">
			
				' . $menus . '
				
			</ul>
		';
		
		return $output;
	}
}