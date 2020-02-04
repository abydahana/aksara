<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Privacy module
 * to save user properties
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Privacy extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
	}
	
	public function index()
	{
		$this->set_title(phrase('security_setting'))
		->set_icon('mdi mdi-account-edit')
		->set_output
		(
			array
			(
				/* basic information */
				'userdata'							=> $this->model->select
				('
					app__users.username,
					app__users.first_name,
					app__users.last_name,
					app__users.photo,
					app__users.bio,
					app__users.address,
					app__users.email,
					app__groups.group_name
				')
				->join
				(
					'app__groups',
					'app__groups.group_id = app__users.group_id'
				)
				->get_where
				(
					'app__users',
					array
					(
						'user_id'					=> get_userdata('user_id')
					),
					1
				)
				->row()
			)
		)
		->render();
	}
}