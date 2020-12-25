<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * APIS > Services
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Services extends Aksara
{
	private $_table									= 'rest__services';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->crud();
		
		$this->_primary								= $this->input->get('id');
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_services'))
		->set_icon('mdi mdi-link-variant')
		->unset_column('id')
		->unset_field('id')
		->unset_view('id')
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->set_field('url', 'dropdown', $this->_modules())
		->set_validation
		(
			array
			(
				'url'								=> 'required|valid_url|is_unique[' . $this->_table . '.url.id.' . $this->_primary . ']',
				'title'								=> 'required|xss_clean|max_length[64]|is_unique[' . $this->_table . '.title.id.' . $this->_primary . ']',
				'description'						=> 'required|xss_clean',
				'status'							=> 'is_boolean'
			)
		)
		->render($this->_table);
	}
	
	private function _modules()
	{
		$current									= $this->model->select('url')->get_where
		(
			$this->_table,
			array
			(
				'id'								=> $this->input->get('id')
			),
			1
		)
		->row('url');
		
		$query										= $this->model->select('url')->get($this->_table)->result();
		$existing									= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$existing[]							= $val->url;
			}
		}
		
		$query										= $this->model->order_by('module, (module != submodule), submodule')->get('app__groups_privileges')->result();
		
		$output										= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$slug								= $val->module;
				
				if($val->module != $val->submodule)
				{
					$slug							.= '/' . $val->submodule;
				}
				
				if($val->submodule != $val->controller)
				{
					$slug							.= '/' . $val->controller;
				}
				
				if(in_array($slug, $existing) && $slug != $current) continue;
				
				$output[$slug]						= $slug;
			}
		}
		
		return $output;
	}
}
