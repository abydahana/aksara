<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * APIS > Permissions
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Permissions extends Aksara
{
	private $_table									= 'rest__permissions';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->_primary								= $this->input->get('id');
		
		if('fetch-parameter' == $this->input->post('method'))
		{
			return $this->_fetch_parameter();
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('client_permissions'))
		->set_icon('mdi mdi-account-check-outline')
		->unset_column('id, parameter')
		->unset_field('id, parameter')
		->unset_view('id, parameter')
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
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
				'PUT'								=> 'PUT ',
				'DELETE'							=> 'DELETE '
			)
		)
		->add_class('url_id', 'fetch-parameter')
		->set_relation
		(
			'url_id',
			'rest__services.id',
			'{rest__services.title}',
			array
			(
				'rest__services.status'					=> 1
			)
		)
		->set_relation
		(
			'client_id',
			'rest__clients.id',
			'{rest__clients.title}',
			array
			(
				'rest__clients.status'				=> 1
			)
		)
		->set_validation
		(
			array
			(
				'title'								=> 'required|xss_clean|is_unique[' . $this->_table . '.title.id.' . $this->_primary . ']',
				'description'						=> 'required|xss_clean',
				'status'							=> 'is_boolean'
			)
		)
		
		->set_alias
		(
			array
			(
				'url_id'							=> phrase('service_or_module'),
				'client_id'							=> phrase('client')
			)
		)
		->render($this->_table);
	}
	
	private function _fetch_parameter()
	{
		$query										= $this->model->select
		('
			rest__services.url
		')
		->join
		(
			'rest__services',
			'rest__services.id = rest__permissions.url_id'
		)
		->get_where
		(
			'rest__permissions',
			array
			(
				'rest__permissions.id'				=> $this->input->post('primary')
			),
			1
		)
		->row('url');
		
		if($query)
		{
			$query									= base_url($query . '/create');
		}
		
		return make_json
		(
			array
			(
				'url'								=> $query
			)
		);
	}
}
