<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * XHR > Language
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Language extends Aksara
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
			$this->session->set_userdata('language', $params);
			$this->session->set_userdata('language_id', $query);
			
			if(get_userdata('is_logged'))
			{
				$this->model->update('app__users', array('language_id' => $query), array('user_id' => get_userdata('user_id')));
			}
		}
		
		return throw_exception(301, null, $_SERVER['HTTP_REFERER'], true);
	}
}
