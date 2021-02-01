<?php
/**
 * Helper file
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.1.19
 */
require_once 'Classes.php';

if(!function_exists('phrase'))
{
	/**
	 * Getting the phrase of translation
	 */
	function phrase($phrase = null)
	{
		// load the classes
		$classes									= new Classes();
		
		return $classes->phrase($phrase);
	}
}

if(!function_exists('random_string'))
{
	/**
	 * Genarate random string
	 */
	function random_string($length = 32, $symbol = false)
	{
		$characters									= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		if($symbol)
		{
			/* add extra symbols */
			$characters								.= '~`!@#%^&*()_-+=|}]{[?/>.<,';
		}
		
		$char_length								= strlen($characters);
		$output										= '';
		
		for($i = 0; $i < $length; $i++)
		{
			$output									.= $characters[rand(0, $char_length - 1)];
		}
		
		return $output;
	}
}
