<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Security module
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Security extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->load->library('form_validation');
	}
	
	public function index()
	{
		/* validate old password */
		$this->form_validation->set_rules('old_password', phrase('current_password'), 'required|callback_old_password_checker');
		$this->form_validation->set_rules('password', phrase('password'), 'required');
		
		$this->set_title(phrase('security_setting'))
		->set_icon('mdi mdi-security')
		->set_method('update')
		->set_field
		(
			array
			(
				'password'							=> 'password'
			)
		)
		->where('user_id', get_userdata('user_id'))
		->render('app__users');
	}
	
	/**
	 * check old password
	 */
	public function old_password_checker($value = null)
	{
		$old_password								= $this->model->select('password')->get_where('app__users', array('user_id' => get_userdata('user_id')), 1)->row('password');
		
		/* check if password is matched */
		if(!password_verify($value . SALT, $old_password))
		{
			$this->form_validation->set_message('old_password_checker', phrase('the_old_password_you_entered_were_incorect'));
			return false;
		}
		
		return true;
	}
}