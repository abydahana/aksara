<?php namespace Aksara\Modules\Galleries\Controllers;
/**
 * Galleries
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Galleries extends \Aksara\Laboratory\Core
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
