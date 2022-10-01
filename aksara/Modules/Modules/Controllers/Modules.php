<?php

namespace Aksara\Modules\Modules\Controllers;

/**
 * Test Aksara module
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Modules extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$segments									= implode(DIRECTORY_SEPARATOR, service('uri')->getSegments());
		$segments									= str_ireplace('modules' . DIRECTORY_SEPARATOR . 'aksara', 'aksara' . DIRECTORY_SEPARATOR . 'Modules', $segments);
		
		if($segments)
		{
			if('php' == strtolower(pathinfo($segments, PATHINFO_EXTENSION)) || !is_file(ROOTPATH . dirname($segments) . DIRECTORY_SEPARATOR . basename($segments)) || !file_exists(ROOTPATH . dirname($segments) . DIRECTORY_SEPARATOR . basename($segments)))
			{
				return throw_exception(404, phrase('the_page_you_requested_was_not_found'), base_url('not_found'));
			}
			
			$path									= ROOTPATH . dirname($segments);
			$filename								= basename($segments);
			$mime									= mime_content_type($path . DIRECTORY_SEPARATOR . $filename);
			
			if('css' == strtolower(pathinfo($filename, PATHINFO_EXTENSION)))
			{
				$mime								= 'text/css';
			}
			else if('js' == strtolower(pathinfo($filename, PATHINFO_EXTENSION)))
			{
				$mime								= 'text/javascript';
			}
			
			service('response')->setContentType($mime);
			service('response')->setBody(file_get_contents($path . DIRECTORY_SEPARATOR . $filename));
			
			return service('response')->send();
		}
	}
}
