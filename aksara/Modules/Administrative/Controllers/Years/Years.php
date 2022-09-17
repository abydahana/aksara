<?php

namespace Aksara\Modules\Administrative\Controllers\Years;

/**
 * Administrative > Years
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Years extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__years';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_primary('year');
	}
	
	public function index()
	{
		$this->set_title(phrase('years'))
		->set_icon('mdi mdi-calendar-clock')
		->set_field
		(
			array
			(
				'default'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_validation
		(
			array
			(
				'year'								=> 'required|numeric|max_length[4]',
				'default'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'year'								=> phrase('year'),
				'default'							=> phrase('default'),
				'status'							=> phrase('status')
			)
		)
		->render($this->_table);
	}
	
	public function before_insert()
	{
		if(service('request')->getPost('default'))
		{
			$this->model->update
			(
				$this->_table,
				array
				(
					'default'						=> 0
				),
				array
				(
					'default'						=> 1
				)
			);
		}
	}
	
	public function before_update()
	{
		if(service('request')->getPost('default'))
		{
			$this->model->update
			(
				$this->_table,
				array
				(
					'default'						=> 0
				),
				array
				(
					'default'						=> 1
				)
			);
		}
	}
}
