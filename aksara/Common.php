<?php
/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

if(! function_exists('aksara'))
{
	/**
	 * Get Aksara variable
	 */
	function aksara($parameter = null)
	{
		$version									= '4.5.0';
		$modified									= filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php');
		
		if('version' == $parameter)
		{
			return $version;
		}
		else if('build_version' == $parameter)
		{
			return $version . \CodeIgniter\CodeIgniter::CI_VERSION;
		}
		else if('date_modified' == $parameter)
		{
			return date('F d Y H:i:s', $modified);
		}
		
		return false;
	}
}

if(! function_exists('get_setting'))
{
	/**
	 * Get setting variable
	 */
	function get_setting($parameter = null)
	{
		$model										= new \Aksara\Laboratory\Model;
		
		if($model->field_exists($parameter, 'app__settings'))
		{
			return $model->select($parameter)->get_where
			(
				'app__settings',
				array
				(
					'id'							=> 1
				),
				1
			)
			->row($parameter);
		}
		
		return false;
	}
}

if(! function_exists('get_userdata'))
{
	/**
	 * Get user session
	 */
	function get_userdata($key = '')
	{
		if(!service('session')->get($key) && service('session')->get('user_id'))
		{
			$model									= new \Aksara\Laboratory\Model;
			$user_id								= service('session')->get('user_id');
			
			if($model->field_exists($key, 'app__users'))
			{
				return $model->select
				(
					$key
				)
				->get_where
				(
					'app__users',
					array
					(
						'user_id'					=> $user_id
					),
					1
				)
				->row($key);
			}
			
			return false;
		}
		
		return service('session')->get($key);
	}
}

if(! function_exists('set_userdata'))
{
	/**
	 * Set user session
	 */
	function set_userdata($key = array(), $value = null)
	{
		if(!is_array($key))
		{
			$key									= array
			(
				$key								=> $value
			);
		}
		
		return service('session')->set($key);
	}
}

if(! function_exists('unset_userdata'))
{
	function unset_userdata($key = array())
	{
		return service('session')->remove($key);
	}
}

if(! function_exists('get_languages'))
{
	/**
	 * Get active languages
	 */
	function get_languages()
	{
		$model										= new \Aksara\Laboratory\Model;
		
		return $model->get_where
		(
			'app__languages',
			array
			(
				'status'							=> 1
			)
		)
		->result();
	}
}

if(! function_exists('phrase'))
{
	/**
	 * Get phrase of translation
	 */
	function phrase($phrase = null, $keep_word = false, $check = false)
	{
		/* make sure the phrase and language is valid */
		if(!$phrase)
		{
			/* otherwise, throwback the null result */
			return false;
		}
		
		$model										= new \Aksara\Laboratory\Model;
		
		/* keep original phrase as given */
		$original_phrase							= $phrase;
		
		/* restrict non alphabet phrase */
		if(!preg_match('/[A-Za-z]/', $phrase))
		{
			return $phrase;
		}
		
		/* transform the phrase into safe-string */
		$phrase										= strtolower(preg_replace('!\s+!', '_', trim(preg_replace('/([^0-9a-z])/i', ' ', $phrase))));
		
		/* get locale by session */
		$language									= get_userdata('language');
		
		/* check if language session isn't available */
		if(!$language)
		{
			$language_id							= (get_userdata('language_id') ? get_userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
			
			$language								= $model->select
			('
				code
			')
			->get_where
			(
				'app__languages',
				array
				(
					'id'							=> $language_id
				)
			)
			->row('code');
		}
		
		$translation_file							= WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $language . '.json';
		
		if(!$check && !file_exists($translation_file))
		{
			if(!is_dir(WRITEPATH . 'translations') && @mkdir(WRITEPATH . 'translations', 0755, true))
			{
				/* put content into file */
				file_put_contents($translation_file, json_encode(array()));
			}
			else if(is_writable(WRITEPATH . 'translations'))
			{
				/* put content into file */
				file_put_contents($translation_file, json_encode(array()));
			}
		}
		
		/* get existing translation */
		$language									= file_get_contents($translation_file);
		
		/* decode json from translation */
		$language									= ($language ? json_decode($language, true) : array());
		
		/* check if language property is valid */
		if(isset($language[$phrase]) && $language[$phrase])
		{
			/* throwback the translated phrase */
			return preg_replace('/"([^<>]*?)"(?=[^>]*?<)/', '&raquo;\1&laquo', str_replace('\'', '&apos;', $language[$phrase]));
		}
		else
		{
			/* skip put new phrase if check is applied */
			if($check)
			{
				/* return formatted phrase */
				return ucfirst(str_replace('_', ' ', $phrase));
			}
			
			/* set new phrase and push into existing */
			$language[$phrase]						= (!$keep_word ? ucfirst(str_replace('_', ' ', $phrase)) : $original_phrase);
			
			/* sort and humanize the order of phrase */
			ksort($language);
			
			/* put new phrase into existing language */
			if(file_exists($translation_file) && is_writable($translation_file))
			{
				file_put_contents($translation_file, json_encode($language, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			}
			
			/* throwback the result */
			return preg_replace('/"([^<>]*?)"(?=[^>]*?<)/', '&raquo;\1&laquo', str_replace('\'', '&apos;', $language[$phrase]));
		}
	}
}
