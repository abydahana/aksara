<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * About module
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class About extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		//$this->set_permission();
		$this->set_method('read');
	}
	
	public function index($params = null)
	{
		/* get user_id by username */
		$user_id									= $this->model->select('user_id')->get_where('app__users', array('username' => $params), 1)->row('user_id');
		
		$this->set_title(phrase('about'))
		->set_icon('mdi mdi-account')
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
						'user_id'					=> $user_id
					),
					1
				)
				->row(),
				
				/* contact information */
				'contacts'							=> $this->model
				->order_by('type')
				->get_where
				(
					'users__contacts',
					array
					(
						'user_id'					=> $user_id
					)
				)
				->result(),
				
				/* educations */
				'educations'						=> $this->model->get_where
				(
					'users__educations',
					array
					(
						'user_id'					=> $user_id
					)
				)
				->result(),
				
				/* educations */
				'workplaces'						=> $this->model->get_where
				(
					'users__workplaces',
					array
					(
						'user_id'					=> $user_id
					)
				)
				->result(),
				
				/* educations */
				'skills'							=> $this->model->get_where
				(
					'users__skills',
					array
					(
						'user_id'					=> $user_id
					)
				)
				->result()
			)
		)
		->render();
	}
}