<?php

namespace Aksara\Modules\Administrative\Controllers\Account;

/**
 * Administrative > Account
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Account extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__users';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->searchable(false);
		
		$this->set_method('update');
	}
	
	public function index()
	{
		if(get_setting('username_changes') || !get_userdata('username'))
		{
			$username_required						= 'required|';
			
			$this->field_order('photo, first_name, last_name, email, username, password, phone, address, postal_code, country_id, language_id');
			$this->merge_field('email, username');
		}
		else
		{
			$username_required						= null;
			
			$this->unset_field('username');
			$this->field_order('photo, first_name, last_name, email, phone, password, address, postal_code, country_id, language_id');
			$this->merge_field('email, phone');
		}
		
		$this->set_title(phrase('account_setting'))
		->set_icon('mdi mdi-account-edit')
		->set_upload_path('users')
		->unset_field('user_id, group_id, registered_date, last_login, status')
		->set_field
		(
			array
			(
				'bio'								=> 'textarea',
				'address'							=> 'textarea',
				'photo'								=> 'image',
				'password'							=> 'password'
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
			'{app__countries.country AS country_name}',
			array
			(
				'app__countries.status'				=> 1
			)
		)
		->merge_field('first_name, last_name')
		->merge_field('country_id, language_id')
		->set_validation
		(
			array
			(
				'first_name'						=> 'required|max_length[32]',
				'last_name'							=> 'max_length[32]',
				'email'								=> 'required|valid_email|unique[app__users.email.user_id.' . get_userdata('user_id') . ']',
				'username'							=> $username_required . 'alpha_dash|unique[app__users.username.user_id.' . get_userdata('user_id') . ']',
				'bio'								=> 'string',
				'address'							=> 'string',
				'language_id'						=> 'required'
			)
		)
		->where('user_id', get_userdata('user_id'))
		->set_alias
		(
			array
			(
				'photo'								=> phrase('photo'),
				'first_name'						=> phrase('first_name'),
				'last_name'							=> phrase('last_name'),
				'email'								=> phrase('email'),
				'phone'								=> phrase('phone'),
				'password'							=> phrase('password'),
				'address'							=> phrase('address'),
				'postal_code'						=> phrase('postal_code'),
				'country_id'						=> phrase('country'),
				'language_id'						=> phrase('language'),
				'bio'								=> phrase('biography')
			)
		)
		
		->render($this->_table);
	}
	
	public function after_update()
	{
		$language									= $this->model->select('code')->get_where
		(
			'app__languages',
			array
			(
				'id'								=> service('request')->getPost('language')
			)
		)
		->row('code');
		
		set_userdata
		(
			array
			(
				'username'							=> (service('request')->getPost('username') ? service('request')->getPost('username') : get_userdata('username')),
				'language'							=> $language
			)
		);
	}
}
