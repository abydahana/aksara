<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Auth > Integrator
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Integrator extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		return make_json
		(
			array
			(
				'user_id'							=> get_userdata('user_id'),
				'group_id'							=> (get_userdata('group_id') <= 3 ? get_userdata('group_id') : 3),
				'username'							=> get_userdata('username'),
				'email'								=> get_userdata('email'),
				'first_name'						=> get_userdata('first_name'),
				'last_name'							=> get_userdata('last_name'),
				'gender'							=> get_userdata('gender'),
				'avatar'							=> get_image('users', get_userdata('photo'), 'thumb'),
				'ip_address'						=> $this->input->ip_address()
			)
		);
	}
}
