<?php

namespace Aksara\Modules\Auth\Controllers;

/**
 * Auth > Integrator
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Integrator extends \Aksara\Laboratory\Core
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
				'ip_address'						=> (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
			)
		);
	}
}
