<?php

namespace Aksara\Modules\Home\Controllers;

/**
 * Home
 * The default landing page of default routes
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Home extends \Aksara\Laboratory\Core
{
	public function index()
	{
		$this->set_title(phrase('welcome_to') . ' ' . get_setting('app_name'))
		->set_description(get_setting('app_description'))
		
		->set_output
		(
			array
			(
				'error'								=> $this->_validate(),
				'permission'						=> array
				(
					'uploads'						=> (is_dir(FCPATH . UPLOAD_PATH) && is_writable(FCPATH . UPLOAD_PATH) ? true : false),
					'writable'						=> (is_dir(WRITEPATH) && is_writable(WRITEPATH) ? true : false)
				)
			)
		)
		
		->render();
	}
	
	/**
	 * this validation indicates the installation whether success or not
	 */
	private function _validate()
	{
		$query										= $this->model->get_where
		(
			'blogs',
			array
			(
			),
			1
		)
		->row();
		
		if($query)
		{
			return true;
		}
		
		return false;
	}
}
