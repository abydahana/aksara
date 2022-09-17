<?php

namespace Aksara\Modules\Administrative\Controllers\Countries;

/**
 * Administrative > Countries
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Countries extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__countries';
	
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
				'code'								=> 'required|alpha_dash|max_length[5]|unique[' . $this->_table . '.code.id.' . $this->_primary . ']',
				'country'							=> 'required|alpha|max_length[32]',
				'status'							=> 'boolean'
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
