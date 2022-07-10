<?php

namespace Aksara\Modules\Cms\Controllers\Comments;

/**
 * CMS > Comments
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.4.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Comments extends \Aksara\Laboratory\Core
{
	private $_table									= 'comments';
	
	public function __construct()
	{
		parent::__construct();
		
		//$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_action('create, update');
	}
	
	public function index()
	{
		$this->set_title(phrase('comments'))
		->set_icon('mdi mdi-comment-multiple-outline')
		
		->render($this->_table);
	}
}
