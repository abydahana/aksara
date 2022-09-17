<?php

namespace Aksara\Modules\Administrative\Controllers\Users;

/**
 * Administrative > Users
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Users extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__users';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_delete('user_id', array(1));
	}
	
	public function index()
	{
		$this->add_filter($this->_filter());
		
		if(service('request')->getGet('group'))
		{
			$this->where
			(
				array
				(
					'group_id'						=> service('request')->getGet('group')
				)
			);
		}
		
		if(in_array($this->_method, array('create')))
		{
			$this->set_validation('password', 'required|min_length[6]');
			$this->set_default('registered_date', date('Y-m-d'));
		}
		
		$this->set_title(phrase('manage_users'))
		->set_icon('mdi mdi-account-group-outline')
		
		->set_primary('user_id, username')
		
		/* add extra option button */
		->add_action('option', 'privileges', phrase('individual_privileges'), 'btn-success --xhr', 'mdi mdi-account-check-outline', array('user_id' => 'user_id'))
		
		->unset_column('user_id, password, phone, gender, bio, address, country, language, postal_code, registered_date')
		->unset_field('user_id, last_login, is_logged, registered_date')
		->unset_view('user_id, password')
		->column_order('photo, username, first_name, email, group_name')
		->field_order('photo, first_name, username, email, password, bio, phone, address, postal_code, country_id, language_id, group_id, status')
		->view_order('photo, first_name, username, email, password, bio, phone, address, postal_code, country, language, group_name, status, last_login')
		->set_field
		( 
			array
			(
				'password'							=> 'password',
				'photo'								=> 'image',
				'last_login'						=> 'datetime',
				'address'							=> 'textarea',
				'bio'								=> 'textarea',
				'is_logged'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_field
		(
			'gender',
			'radio',
			array
			(
				0									=> phrase('male'),
				1									=> phrase('female')
			)
		)
		->set_relation
		(
			'language_id',
			'app__languages.id',
			'{app__languages.language}',
			array
			(
				'app__languages.status'				=> 1
			)
		)
		->set_relation
		(
			'country_id',
			'app__countries.id',
			'{app__countries.country}',
			array
			(
				'app__countries.status'				=> 1
			)
		)
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
		->set_validation
		(
			array
			(
				'first_name'						=> 'required|string|max_length[32]',
				'last_name'							=> 'string|max_length[32]',
				'username'							=> 'required|alpha_numeric|unique[app__users.username.user_id.' . service('request')->getGet('user_id') . ']',
				'email'								=> 'required|valid_email|unique[app__users.email.user_id.' . service('request')->getGet('user_id') . ']',
				'language_id'						=> 'required',
				'group_id'							=> 'required',
				'status'							=> 'boolean'
			)
		)
		->field_position
		(
			array
			(
				'bio'								=> 2,
				'postal_code'						=> 2,
				'address'							=> 2,
				'phone'								=> 2,
				'country_id'						=> 2,
				'country_name'						=> 2,
				'language_id'						=> 2,
				'language'							=> 2,
				'group_id'							=> 2,
				'group_name'						=> 2,
				'pin'								=> 2,
				'status'							=> 2,
				'last_login'						=> 2
			)
		)
		->set_alias
		(
			array
			(
				'first_name'						=> phrase('first_name'),
				'last_name'							=> phrase('last_name'),
				'username'							=> phrase('username'),
				'email'								=> phrase('email'),
				'password'							=> phrase('password'),
				'phone'								=> phrase('phone'),
				'bio'								=> phrase('bio'),
				'address'							=> phrase('address'),
				'postal_code'						=> phrase('postal_code'),
				'country_id'						=> phrase('country'),
				'country'							=> phrase('country'),
				'language_id'						=> phrase('language'),
				'language'							=> phrase('language'),
				'group_id'							=> phrase('group'),
				'group_name'						=> phrase('group')
			)
		)
		
		->merge_content('{first_name} {last_name}', phrase('full_name'))
		->merge_field('first_name, last_name', phrase('full_name'))
		
		->render($this->_table);
	}
	
	private function _filter()
	{
		$output										= '<option value="0">' . phrase('all_groups') . '</option>';
		
		$query										= $this->model->get_where
		(
			'app__groups',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$output								.= '<option value="' . $val->group_id . '"' . ($val->group_id == service('request')->getGet('group') ? ' selected' : null) . '>' . $val->group_name . '</option>';
			}
		}
		
		return '
			<select name="group" class="form-control form-control-sm bordered" placeholder="' . phrase('group') . '">
				' . $output . '
			</select>
		';
	}
}
