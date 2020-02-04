<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Account module
 * to save user properties
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Account extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_method('update');
		$this->unset_action('create, read, delete, export, print, pdf');
		$this->parent_module('dashboard');
	}
	
	public function index()
	{
		if(get_setting('enable_username_changes'))
		{
			$username_required						= 'required|';
		}
		else
		{
			$username_required						= null;
			$this->unset_field('username');
		}
		
		$this->set_title(phrase('account_setting'))
		->set_icon('mdi mdi-account-edit')
		->set_upload_path('users')
		->unset_field('user_id, group_id, registered_date, last_login, status')
		->field_order('photo, first_name, last_name, email, phone, password, address, postal_code, country, language_id')
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
			'country',
			'countries.id',
			'{countries.country AS country_name}',
			array
			(
				'countries.status'					=> 1
			)
		)
		->merge_field('first_name, last_name')
		->merge_field('email, phone')
		->merge_field('country, language_id')
		->set_validation
		(
			array
			(
				'first_name'						=> 'required|alpha_numeric_spaces|max_length[32]',
				'last_name'							=> 'alpha_numeric_spaces|max_length[32]',
				'email'								=> 'required|valid_email|is_unique[app__users.email.user_id.' . get_userdata('user_id') . ']',
				'username'							=> $username_required . 'alpha_dash|is_unique[app__users.username.user_id.' . get_userdata('user_id') . ']',
				'bio'								=> 'xss_clean',
				'address'							=> 'xss_clean',
				'country'							=> 'required',
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
				'country'							=> phrase('country'),
				'language_id'						=> phrase('language'),
				'bio'								=> phrase('biography')
			)
		)
		->render('app__users');
	}
	
	public function after_update()
	{
		$language									= $this->model->select('code')->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $this->input->post('language')
			)
		)
		->row('code');
		
		$this->session->set_userdata
		(
			array
			(
				'username'							=> ($this->input->post('username') ? $this->input->post('username') : get_userdata('username')),
				'language'							=> $language
			)
		);
	}
}