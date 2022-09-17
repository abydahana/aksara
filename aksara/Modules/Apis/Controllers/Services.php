<?php

namespace Aksara\Modules\Apis\Controllers;

/**
 * APIS > Services
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Services extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__rest_api';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->_primary								= service('request')->getGet('id');
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_services'))
		->set_icon('mdi mdi-link-variant')
		->unset_column('id, method, ip_range')
		->unset_field('id')
		->unset_view('id')
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'ip_range'							=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->set_field
		(
			'method',
			'checkbox',
			array
			(
				'GET'								=> 'GET ',
				'POST'								=> 'POST ',
				'DELETE'							=> 'DELETE '
			)
		)
		->default_value('api_key', $this->_api_key_generator())
		->merge_field('valid_until, status')
		->set_validation
		(
			array
			(
				'title'								=> 'required|string|max_length[64]|unique[' . $this->_table . '.title.id.' . $this->_primary . ']',
				'description'						=> 'required|string',
				'api_key'							=> 'required|alpha_numeric|min_length[32]',
				'method'							=> 'required|in_list[GET,POST,DELETE]',
				'valid_until'						=> 'required|valid_date',
				'status'							=> 'boolean'
			)
		)
		
		->set_alias
		(
			array
			(
				'title'								=> phrase('title'),
				'description'						=> phrase('description'),
				'api_key'							=> phrase('api_key'),
				'ip_range'							=> phrase('ip_range'),
				'method'							=> phrase('request_method'),
				'valid_until'						=> phrase('valid_until'),
				'status'							=> phrase('status')
			)
		)
		
		->render($this->_table);
	}
	
	private function _api_key_generator($length = 32)
	{
		$characters									= '0123456789ABCDEF';
		
		$char_length								= strlen($characters);
		$output										= '';
		
		for($i = 0; $i < $length; $i++)
		{
			$output									.= $characters[rand(0, $char_length - 1)];
		}
		
		$exist										= $this->model->select
		('
			api_key
		')
		->get_where
		(
			$this->_table,
			array
			(
				'api_key'							=> $output
			),
			1
		)
		->row('api_key');
		
		if($exist)
		{
			$this->_api_key_generator();
		}
		
		return $output;
	}
}
