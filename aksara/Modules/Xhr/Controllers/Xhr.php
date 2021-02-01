<?php namespace Aksara\Modules\Xhr\Controllers;
/**
 * XHR
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Xhr extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		return throw_exception(404, phrase('the_page_you_requested_was_not_found_or_it_is_already_removed'));
	}
}
