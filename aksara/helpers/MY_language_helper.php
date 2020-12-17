<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Language Helper
 * Override the default CodeIgniter language_helper to follow the local parameter
 *
 * @package		Language
 * @version		2.0.1
 * @author 		Aby Dahana <abydahana@gmail.com>
 * @copyright 	Copyright (c) 2018, Aby Dahana
 * @link		https://www.facebook.com/abyprogrammer
**/

if (!function_exists('phrase'))
{
	function phrase($phrase = null, $keep_word = false)
	{
		/* keep original phrase as given */
		$original_phrase							= $phrase;
		
		/* restrict non alphabet phrase */
		if(!preg_match('/[A-Za-z]/', $phrase))
		{
			return $phrase;
		}
		
		/* transform the phrase into safe-string */
		$phrase										= strtolower(preg_replace('!\s+!', '_', trim(preg_replace('/([^0-9a-z])/i', ' ', $phrase))));
		
		/* make sure the phrase and language is valid */
		if(!$phrase)
		{
			/* otherwise, throwback the null result */
			return false;
		}
		
		/* get system instance */
		$CI											=& get_instance();
		
		/* get locale by session */
		$language_id								= (get_userdata('language_id') ? get_userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
		
		$language									= $CI->db->select
		('
			code
		')
		->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $language_id
			)
		)
		->row('code');
		
		$translation_file							= TRANSLATION_PATH . DIRECTORY_SEPARATOR . $language . '.json';
		
		if(!file_exists($translation_file))
		{
			if(!is_dir(TRANSLATION_PATH) && @mkdir(TRANSLATION_PATH, 0755, true))
			{
				/* put content into file */
				file_put_contents($translation_file, json_encode(array()));
			}
			elseif(is_writable(TRANSLATION_PATH))
			{
				/* put content into file */
				file_put_contents($translation_file, json_encode(array()));
			}
		}
		
		/* get existing translation */
		$language									= file_get_contents($translation_file);
		
		/* decode json from translation */
		$language									= json_decode($language, true);
		
		/* check if language property is valid */
		if(isset($language[$phrase]))
		{
			/* throwback the translated phrase */
			return str_replace('\'', '&apos;', $language[$phrase]);
		}
		else
		{
			/* set new phrase and push into existing */
			$language[$phrase]						= (!$keep_word ? ucwords(str_replace('_', ' ', $phrase)) : $original_phrase);
			
			/* sort and humanize the order of phrase */
			ksort($language);
			
			/* put new phrase into existing language */
			if(file_exists($translation_file) && is_writable($translation_file))
			{
				file_put_contents($translation_file, json_encode($language));
			}
			
			/* throwback the result */
			return str_replace('\'', '', $language[$phrase]);
		}
	}
}

if (!function_exists('get_languages'))
{
	/**
	 * List the available language
	 */
	function get_languages()
	{
		$CI											=& get_instance();
		
		return $CI->db->select
		('
			id,
			code,
			language
		')
		->order_by('language')
		->get_where
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

if (!function_exists('set_user_language'))
{
	/**
	 * List the available language
	 */
	function set_user_language()
	{
		$CI											=& get_instance();
		
		if(get_userdata('language_id'))
		{
			return get_userdata('language_id');
		}
		else
		{
			$locale									= (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) : 'en-us');
			$language_id							= $CI->db->select
			('
				id
			')
			->like('locale', (isset($locale[0]) ? $locale[0] : '%'))
			->get_where
			(
				'app__languages',
				array
				(
					'status'						=> 1
				),
				1
			)
			->row('id');
			
			if($language_id)
			{
				$CI->session->set_userdata('language_id', $language_id);
				
				return $language_id;
			}
			
			return 1;
		}
	}
}
