<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Synchronize Phrase
 * This module used to synchronize the phrase on translation file and merging
 * new phrase to the existing.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Synchronize extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		if(defined('DEMO_MODE') && DEMO_MODE)
		{
			return throw_exception(301, phrase('changes_will_not_saved_in_demo_mode'), current_page('../'));
		}
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		$this->parent_module('translations');
	}
	
	public function index()
	{
		/* load the additional helper */
		$this->load->helper('file');
		
		/* list the file inside the language folder */
		$languages									= get_filenames('public/languages');
		$phrases									= array();
		$error										= 0;
		
		if($languages)
		{
			/* merge phrase from whole translation to one variable */
			foreach($languages as $key => $val)
			{
				/* skip if not valid translation */
				if(strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') continue;
				
				/* get translation */
				$phrase								= file_get_contents('public/languages/' . $val);
				
				/* decode phrases */
				$phrase								= json_decode($phrase, true);
				
				/* merge phrases */
				$phrases							= array_merge($phrases, $phrase);
			}
			
			/* prepare to push phrases to translation */
			foreach($languages as $key => $val)
			{
				/* skip if not valid translation */
				if(strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') continue;
				
				/* get translation */
				$phrase								= file_get_contents('public/languages/' . $val);
				
				/* decode phrases */
				$phrase								= json_decode($phrase, true);
				
				/* add new phrase */
				foreach($phrases as $_key => $_val)
				{
					/* push phrase into existing if not exists */
					if(!isset($phrase[$_key]))
					{
						$phrase[$_key]				= ucwords(str_replace('_', ' ', $_key));
					}
				}
				
				/* sort and humanize the order of phrase */
				ksort($phrase);
				
				/* try to add language file */
				try
				{
					/* put content into file */
					file_put_contents('public/languages/' . $val, json_encode($phrase));
				}
				catch(Exception $e)
				{
					/* failed to write file, throw an error exception */
					$error++;
				}
			}
		}
		
		if($error)
		{
			return throw_exception(301, phrase('phrase_synchronized') . ', ' .  phrase('however') . ' ' . phrase('there_is') . ' <b>' . number_format($error) . '</b> ' . phrase('were_unsuccessful'), current_page('../'));
		}
		
		return throw_exception(301, '<b>' . (sizeof($languages) - 1) . '</b> ' . ((sizeof($languages) - 1) > 1 ? phrase('languages') : phrase('language')) . ' ' . phrase('was_successfully_updated') . ', <b>' . number_format(sizeof($phrases)) . '</b> ' . phrase('phrase_synchronized'), current_page('../'));
	}
}