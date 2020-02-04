<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Travelling > Detail
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Detail extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('detail'))
		->render();
	}
}
