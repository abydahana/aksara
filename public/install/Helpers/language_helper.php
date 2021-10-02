<?php
/**
 * Language Helper
 * A helper to translate language by phrase
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.8
 * @copyright		(c) 2021 - Aksara Laboratory
 */

if(!function_exists('phrase'))
{
	/**
	 * Generate security token to validate the query string values
	 */
	function phrase($phrase = null)
	{
		if($phrase)
		{
			return lang('Install.' . $phrase);
		}
	}
}
