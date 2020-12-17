<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * XHR
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Xhr extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		return throw_exception(404, phrase('the_page_you_requested_was_not_found_or_it_is_already_removed'));
	}
}
