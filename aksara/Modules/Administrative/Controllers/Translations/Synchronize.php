<?php

namespace Aksara\Modules\Administrative\Controllers\Translations;

/**
 * Administrative > Translations > Synchronize
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Synchronize extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		if(DEMO_MODE)
		{
			return throw_exception(403, phrase('changes_will_not_saved_in_demo_mode'), current_page('../'));
		}
		
		$this->set_permission();
		$this->set_theme('backend');
	}
	
	public function index()
	{
		/* load the additional helper */
		helper('filesystem');
		
		/* list the file inside the language folder */
		$languages									= get_filenames(WRITEPATH . 'translations');
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
				$phrase								= file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
				
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
				$phrase								= file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
				
				/* decode phrases */
				$phrase								= json_decode($phrase, true);
				
				/* add new phrase */
				foreach($phrases as $_key => $_val)
				{
					/* push phrase into existing if not exists */
					if(!isset($phrase[$_key]))
					{
						$phrase[$_key]				= ucfirst(str_replace('_', ' ', $_key));
					}
				}
				
				/* sort and humanize the order of phrase */
				ksort($phrase);
				
				/* try to add language file */
				try
				{
					file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrase, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				}
				catch(\Throwable $e)
				{
					/* failed to write file, throw an error exception */
					$error++;
				}
			}
		}
		
		if($error)
		{
			return throw_exception(403, phrase('phrase_synchronized') . ' ' .  phrase('however') . ' ' . phrase('there_are') . ' <b>' . number_format($error) . '</b> ' . phrase('were_unsuccessful'), current_page('../'));
		}
		
		return throw_exception(301, '<b>' . (sizeof($languages) - 1) . '</b> ' . ((sizeof($languages) - 1) > 1 ? phrase('languages') : phrase('language')) . ' ' . phrase('was_successfully_updated') . ' <b>' . number_format(sizeof($phrases)) . '</b> ' . phrase('phrase_synchronized'), current_page('../'));
	}
}
