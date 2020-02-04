<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Culinary
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Culinary extends Aksara
{
	private $_table									= 'guide__culinary';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('culinary'))
		->render($this->_table);
	}
}
