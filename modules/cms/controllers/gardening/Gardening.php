<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Gardening
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Gardening extends Aksara
{
	private $_table									= 'guide__gardening';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('gardening'))
		->render($this->_table);
	}
}
