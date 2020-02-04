<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Travelling
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Travelling extends Aksara
{
	private $_table									= 'guide__travelling';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('traveling'))
		->render($this->_table);
	}
}
