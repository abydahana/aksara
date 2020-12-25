<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Administrative > Inquiries
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Inquiries extends Aksara
{
	private $_table									= 'inquiries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->crud();
		
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
		
		->render($this->_table);
	}
}
