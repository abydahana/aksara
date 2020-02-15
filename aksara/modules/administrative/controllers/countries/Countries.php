<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Country Management
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Countries extends Aksara
{
	private $_table									= 'countries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		
		$this->_primary								= $this->input->get('id');
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_countries'))
		->set_icon('mdi mdi-map-legend')
		->unset_column('id')
		->unset_field('id')
		->unset_view('id')
		->set_field('status', 'boolean')
		->set_validation
		(
			array
			(
				'code'								=> 'required|alpha_dash|max_length[5]|is_unique[' . $this->_table . '.code.id.' . $this->_primary . ']',
				'country'							=> 'required|alpha|max_length[32]',
				'status'							=> 'is_boolean'
			)
		)
		->set_alias
		(
			array
			(
				'code'								=> phrase('code'),
				'country'							=> phrase('country'),
				'status'							=> phrase('status')
			)
		)
		->render($this->_table);
	}
}