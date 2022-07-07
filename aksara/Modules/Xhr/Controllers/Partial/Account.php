<?php

namespace Aksara\Modules\Xhr\Controllers\Partial;

/**
 * XHR > Partial > Account
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.8
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Account extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		if('modal' != service('request')->getPost('prefer'))
		{
			return throw_exception(404, phrase('the_page_you_requested_does_not_exist'));
		}
		else if(!get_userdata('user_id'))
		{
			return throw_exception(403, phrase('you_do_not_have_a_sufficient_privileges_to_access_the_requested_page'), base_url());
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('option'))
		->set_icon('mdi mdi-cogs')
		
		->set_output
		(
			array
			(
				'user'								=> $this->_user()
			)
		)
		
		->render();
	}
	
	private function _user()
	{
		$query										= $this->model->get_where
		(
			'app__users',
			array
			(
				'user_id'							=> get_userdata('user_id')
			)
		)
		->result();
		
		return $query;
	}
}
