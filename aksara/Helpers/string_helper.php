<?php
/**
 * String Helper
 * A helper that sanitize the string
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

if(!function_exists('truncate'))
{
	/**
	 * Truncate the string
	 *
	 * @params		string		$string
	 * @params		int			$limit
	 * @params		string		$break
	 * @params		string		$pad
	 */
	function truncate($string = null, $limit = 0, $break = '.', $pad = '...')
	{
		$string										= preg_replace('/<script.*?\/script>/i','', $string);
		$string										= preg_replace('/<noscript.*?\/noscript>/i','', $string);
		$string										= preg_replace('/<style.*?\/style>/i','', $string);
		$string										= preg_replace('/<link.*/i','', $string);
		$string										= preg_replace('/<iframe.*?\/iframe>/i','', $string);
		$string										= preg_replace('/<embed.*?\/embed>/i','', $string);
		$string										= preg_replace('/<object.*?\/object>/i','', $string);
		$string										= str_replace('&nbsp;', ' ', $string);
		$string										= htmlspecialchars(str_replace(array("\r", "\n"), '', $string));
		
		if($limit && strlen($string) >= $limit)
		{
			$string									= substr($string, 0, $limit) . $pad;
		}
		
		return $string;
	}
}

if(!function_exists('is_json'))
{
	/**
	 * Check if JSON is valid
	 *
	 * @params		string		$string
	 */
	function is_json($string = null)
	{
		if(is_string($string))
		{
			$string									= json_decode($string, true);
			
			if(json_last_error() == JSON_ERROR_NONE)
			{
				return $string;
			}
			else
			{
				return array();
			}
		}
		else
		{
			return array();
		}
	}
}

if(!function_exists('make_json'))
{
	/**
	 * Generate the response as JSON format
	 *
	 * @data		mixed		array|object
	 * @filename	string		response will be downloaded and named as its value
	 */
	function make_json($data = array(), $filename = null)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		
		$data										= (object) $data;
		$html										= null;
		
		if(isset($data->html))
		{
			$html									= $data->html;
			
			/* make a backup of "pre" tag */
			preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $html, $pre_backup);
			$html									= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $html);
			
			$html									= preg_replace(array('/[ \t]+/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\>)\s*(\<)/m'), array(' ', '>', '<', '$1$2'), $html);
			
			/* rollback the pre tag */
			$html									= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $html);
		}
		
		if($html)
		{
			$data->html								= $html;
		}
		
		$data										= json_fixer($data);
		
		$data										= preg_replace('/\t/', '', json_encode($data));
		
		if($filename)
		{
			header('Content-disposition: attachment; filename=' . $filename . '.json');
		}
		
		header('Content-Type: application/json');
		
		exit($data);
	}
}

if(!function_exists('json_fixer'))
{
	/**
	 * Fix malformed UTF-8 characters, possibly incorrectly encoded
	 * json return
	 */
	function json_fixer($data = '')
	{
		if(is_string($data))
		{
			return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
		}
		elseif(is_array($data))
		{
			$output									= array();
			
			foreach($data as $key => $val)
			{
				$output[$key]						= json_fixer($val);
			}
			
			return $output;
		}
		else
		{
			return $data;
		}
	}
}

if(!function_exists('time_ago'))
{
	function time_ago($datetime = null, $full = false)
	{
		$now										= new \DateTime;
		$ago										= new \DateTime($datetime);
		$diff										= $now->diff($ago);

		$diff->w									= floor($diff->d / 7);
		$diff->d									-= $diff->w * 7;

		$string										= array
		(
			'y'										=> phrase('year'),
			'm'										=> phrase('month'),
			'w'										=> phrase('week'),
			'd'										=> phrase('day'),
			'h'										=> phrase('hour'),
			'i'										=> phrase('minute'),
			's'										=> phrase('second'),
		);
		
		foreach($string as $k => &$v)
		{
			if($diff->$k)
			{
				$v									= $diff->$k . ' ' . $v . ($diff->$k > 1 ? strtolower(phrase('s')) : '');
			}
			else
			{
				unset($string[$k]);
			}
		}

		if(!$full)
		{
			$string								= array_slice($string, 0, 1);
		}
		
		return $string ? implode(', ', $string) . ' ' . phrase('ago') : phrase('just_now');
	}
}
