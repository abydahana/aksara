<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Galleries
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Galleries extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('our_gallery_activities'))
		->set_description(phrase('our_gallery_activities'))
		->set_icon('mdi mdi-folder-multiple-image')
		->set_primary('gallery_slug')
		->order_by('gallery_title', 'RANDOM')
		->where('status', 1)
		->render('galleries');
	}
}
