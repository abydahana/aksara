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
		
		$this->crud();
		
		$this->_primary								= $this->input->get('client');
		
		if('fetch-parameter' == $this->input->post('method'))
		{
			return $this->_fetch_parameter();
		}
	}
	
	public function index()
	{
		if($this->_primary)
		{
			$query									= $this->model->select
			('
				app__users.first_name,
				app__users.last_name
			')
			->join
			(
				'app__users',
				'app__users.user_id = rest__clients.user_id'
			)
			->get_where
			(
				'rest__clients',
				array
				(
					'rest__clients.user_id'			=> $this->_primary
				),
				1
			)
			->row();
			
			if($query)
			{
				$this->set_description
				('
					<div class="row">
						<div class="col-4 col-sm-3 col-md-2 text-muted text-uppercase">
							' . phrase('client') . '
						</div>
						<div class="col-8 col-sm-9 col-md-4 font-weight-bold">
							' . $query->first_name . ' ' . $query->last_name . '
						</div>
					</div>
				');
				
				$this->set_default
				(
					array
					(
						'client_id'					=> $this->_primary
					)
				)
				->where
				(
					array
					(
						'client_id'					=> $this->_primary
					)
				)
				->unset_column('first_name')
				->unset_field('client_id');
			}
		}
		
		if(!$this->input->post('_token'))
		{
			$this->unset_field('parameter');
		}
		
		$this->set_title(phrase('client_permissions'))
		->set_icon('mdi mdi-security-network')
		->unset_column('id, method, parameter')
		->unset_field('id')
		->unset_view('id')
		->column_order('title, description, method, first_name')
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
				'DELETE'							=> 'DELETE '
			)
		)
		->add_class('service_id', 'fetch-parameter')
		->set_relation
		(
			'service_id',
			'rest__services.id',
			'{rest__services.title}',
			array
			(
				'rest__services.status'				=> 1
			)
		)
		->set_relation
		(
			'client_id',
			'rest__clients.user_id',
			'{app__users.first_name} {app__users.last_name}',
			array
			(
				'rest__clients.status'				=> 1,
				'app__users.status'					=> 1
			),
			array
			(
				array
				(
					'app__users',
					'app__users.user_id = rest__clients.user_id'
				)
			)
		)
		->set_validation
		(
			array
			(
				'title'								=> 'required|xss_clean|is_unique[' . $this->_table . '.title.id.' . $this->input->get('id') . ']',
				'description'						=> 'required|xss_clean',
				'method'							=> 'required|in_list[GET,POST,DELETE]',
				'status'							=> 'is_boolean'
			)
		)
		
		->set_alias
		(
			array
			(
				'service_id'						=> phrase('service_or_module'),
				'client_id'							=> phrase('client'),
				'title'								=> phrase('title'),
				'description'						=> phrase('description'),
				'method'							=> phrase('request_method'),
				'status'							=> phrase('status')
			)
		)
		->merge_content('{first_name} {last_name}', phrase('client'))
		->render($this->_table);
	}
	
	private function _fetch_parameter()
	{
		$query										= $this->model->select
		('
			rest__services.url,
			rest__permissions.parameter
		')
		->join
		(
			'rest__services',
			'rest__services.id = rest__permissions.service_id'
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
		->row();
		
		if($query)
		{
			$query->url								= base_url($query->url . '/create');
		}
		
		return make_json
		(
			array
			(
				'url'								=> (isset($query->url) ? $query->url : null),
				'parameter'							=> (isset($query->parameter) ? $query->parameter : null)
			)
		);
	}
}
