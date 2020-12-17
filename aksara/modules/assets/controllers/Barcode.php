<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Assets > Barcode
 * Convert the given parameter to barcode (png)
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Barcode extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->library('zend');
		$this->zend->load('Zend/Barcode');
		
		return Zend_Barcode::render
		(
			'code128',
			'image',
			array
			(
				'text'								=> $this->input->get('code')
			)
		);
	}
}
