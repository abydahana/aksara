<?php

namespace Aksara\Modules\Cms\Controllers\Partials;

/**
 * CMS > Partials > Inquiries
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Inquiries extends \Aksara\Laboratory\Core
{
	private $_table									= 'inquiries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_action('create, update');
	}
	
	public function index()
	{
		$this->set_title(phrase('inquiries'))
		->set_icon('mdi mdi-message-text')
		->unset_column('id')
		->unset_field('id')
		->unset_view('id')
		
		->set_alias
		(
			array
			(
				'sender_email'						=> phrase('email'),
				'sender_full_name'					=> phrase('sender')
			)
		)
		
		->order_by('timestamp', 'DESC')
		
		->render($this->_table);
	}
}
