<?php

namespace Aksara\Modules\Shortlink\Controllers;

/**
 * Shortlink
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.5
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Shortlink extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params = '')
	{
		$query										= null;
		
		if($this->model->table_exists('app__shortlink'))
		{
			$query									= $this->model->get_where
			(
				'app__shortlink',
				array
				(
					'hash'							=> $params
				),
				1
			)
			->row();
		}
		
		if($query)
		{
			/* set the one time temporary session */
			if(!get_userdata('is_logged'))
			{
				$data								= json_decode($query->data, true);
				$session['sess_destroy_after']		= 'once';
				
				set_userdata($data);
			}
			
			/* redirect to real URL */
			return throw_exception(301, null, $query->url);
		}
		else
		{
			return throw_exception(404, phrase('the_page_you_requested_does_not_exist'), base_url());
		}
	}
}
