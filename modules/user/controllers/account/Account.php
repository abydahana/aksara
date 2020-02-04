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
		->set_method('update')
		->set_upload_path('users')
		->set_field
		(
			array
			(
				'bio'								=> 'textarea',
				'address'							=> 'textarea',
				'photo'								=> 'image'
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
		->set_relation
		(
			'language',
			'app__languages.code',
			'{app__languages.language AS language_name}'
		)
		->set_validation
		(
			array
			(
				'first_name'						=> 'required|alpha|max_length[32]',
				'last_name'							=> 'alpha|max_length[32]',
				'email'								=> 'required|valid_email|is_unique[app__users.email.user_id.' . get_userdata('user_id') . ']',
				'username'							=> $username_required . 'alpha_dash|is_unique[app__users.username.user_id.' . get_userdata('user_id') . ']',
				'bio'								=> 'xss_clean',
				'address'							=> 'xss_clean',
				'country'							=> 'required',
				'language'							=> 'required'
			)
		)
		->where('user_id', get_userdata('user_id'))
		->render('app__users');
	}
	
	public function after_update()
	{
		$this->session->set_userdata
		(
			array
			(
				'username'							=> ($this->input->post('username') ? $this->input->post('username') : get_userdata('username')),
				'language'							=> $this->input->post('language')
			)
		);
	}
}