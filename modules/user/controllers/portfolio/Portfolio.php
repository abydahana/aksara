<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Portfolio module
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Portfolio extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_username							= ($this->uri->segment(2) ? $this->uri->segment(2) : get_userdata('username'));
		$this->_primary								= $this->model->select('user_id, first_name, last_name')->get_where('app__users', array('username' => $this->_username), 1)->row();
		
		/* check if user is exists */
		if(!$this->_primary)
		{
			return throw_exception(404, phrase('user_was_not_found_or_account_is_temporary_blocked'), base_url());
		}
		
		$this->_title								= $this->_primary->first_name . ' ' . $this->_primary->last_name;
		$this->_primary								= $this->_primary->user_id;
	}
	
	public function index($params = null)
	{
		$this->set_title($this->_title . ' Portfolio')
		->set_icon('mdi mdi-briefcase-account-outline')
		->set_output
		(
			array
			(
				/* get userdata */
				'userdata'							=> $this->model->select
				('
					app__users.username,
					app__users.first_name,
					app__users.last_name,
					app__users.photo,
					app__users.address,
					app__users.bio,
					app__users.email,
					app__users.registered_date,
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
						'app__users.user_id'		=> $this->_primary
					),
					1
				)
				->row()
			)
		)
		->render('users__portfolios');
	}
}
