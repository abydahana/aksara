<?php namespace Aksara\Modules\Shortlink\Controllers;
/**
 * Shortlink
 *
 * @author			Aby Dahana
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
	
	public function index()
	{
		$query										= null;
		
		if(!$this->model->table_exists('app__shortlink'))
		{
			$query									= $this->model->select
			('
				url
			')
			->get_where
			(
				'app__shortlink',
				array
				(
					'hash'							=> $params
				),
				1
			)
			->row('url');
		}
		
		redirect_to($query);
	}
}
