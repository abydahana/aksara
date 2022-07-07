<?php

namespace Aksara\Modules\Xhr\Controllers;

/**
 * XHR > Language
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Language extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->permission->must_ajax(base_url());
	}
	
	public function index($params = null)
	{
		$query										= $this->model->select('id')->get_where
		(
			'app__languages',
			array
			(
				'code'								=> $params
			),
			1
		)
		->row('id');
		
		if($query)
		{
			set_userdata('language', $params);
			set_userdata('language_id', $query);
			
			if(get_userdata('is_logged'))
			{
				$this->model->update('app__users', array('language_id' => $query), array('user_id' => get_userdata('user_id')));
			}
		}
		
		return throw_exception(301, null, service('request')->getServer('HTTP_REFERER'), true);
	}
}
