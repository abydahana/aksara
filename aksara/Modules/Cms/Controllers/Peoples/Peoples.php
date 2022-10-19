<?php

namespace Aksara\Modules\Cms\Controllers\Peoples;

/**
 * CMS > Peoples
 * Manage peoples that will be shown in the frontpage
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Peoples extends \Aksara\Laboratory\Core
{
	private $_table									= 'peoples';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('peoples'))
		->set_icon('mdi mdi-account-group-outline')
		->unset_column('people_id, people_slug, biography, instagram, facebook, twitter')
		->unset_field('people_id')
		->unset_view('people_id')
		->set_field
		(
			array
			(
				'biography'							=> 'textarea',
				'email'								=> 'email',
				'photo'								=> 'image',
				'status'							=> 'boolean'
			)
		)
		->set_field('people_slug', 'to_slug', 'full_name')
		->set_field('full_name', 'hyperlink', 'peoples', array('people_slug' => 'people_slug'), true)
		
		->add_action('option', '../../peoples/user', phrase('view_page'), 'btn-success', 'mdi mdi-eye', array('people_slug' => 'people_slug'), true)
		
		->column_order('photo, full_name')
		->field_order('photo')
		->set_validation
		(
			array
			(
				'first_name'						=> 'required|string',
				'last_name'							=> 'string',
				'people_slug'						=> 'max_length[64]|unique[' . $this->_table . '.people_slug.people_id.' . service('request')->getGet('people_id') . ']',
				'status'							=> 'boolean'
			)
		)
		->merge_field('first_name, last_name')
		->merge_field('mobile, instagram')
		->merge_field('facebook, twitter')
		->merge_content('{first_name} {last_name}', phrase('full_name'))
		->field_size
		(
			array
			(
				'mobile'							=> 'col-md-6',
				'instagram'							=> 'col-md-6',
				'facebook'							=> 'col-md-6',
				'twitter'							=> 'col-md-6'
			)
		)
		->set_alias
		(
			array
			(
				'photo'								=> phrase('photo'),
				'first_name'						=> phrase('first_name'),
				'last_name'							=> phrase('last_name'),
				'people_slug'						=> phrase('slug'),
				'position'							=> phrase('position'),
				'mobile'							=> phrase('mobile'),
				'email'								=> phrase('email'),
				'biography'							=> phrase('biography'),
				'status'							=> phrase('status')
			)
		)
		
		->render($this->_table);
	}
}
