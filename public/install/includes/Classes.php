<?php
/**
 * Classes file
 * Handle the instance that required to helper
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.1.19
 */
class Classes
{
	public function __construct()
	{
		// check if the session is sets to available translation
		if(isset($_SESSION['language']) && in_array($_SESSION['language'], array('en', 'id')))
		{
			$language								= $_SESSION['language'];
		}
		else
		{
			$language								= 'en';
		}
		
		// include the multilingual translation
		$this->phrase								= include dirname(__DIR__) . '/languages/' . $language . '.php';
	}
	
	/**
	 * Getting the phrase of translation
	 */
	public function phrase($phrase = null)
	{
		if(isset($this->phrase[$phrase]))
		{
			// return the phrase from translation file
			return $this->phrase[$phrase];
		}
		
		return ucwords(str_replace('_', ' ', $phrase));
	}
}
