<?php

namespace Aksara\Modules\Xhr\Controllers\Partial;

/**
 * XHR > Partial > Language
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.8
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Language extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		if('modal' != service('request')->getPost('prefer'))
		{
			return throw_exception(404, phrase('the_page_you_requested_does_not_exist'));
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('change_language'))
		->set_icon('mdi mdi-translate')
		
		->set_output
		(
			array
			(
				'languages'							=> $this->_languages()
			)
		)
		
		->render();
	}
	
	private function _languages()
	{
		$query										= $this->model->get_where
		(
			'app__languages',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		return $query;
	}
}
