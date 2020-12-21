<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Global Helper
 *
 * This helper is preparing all required data for site needs
 * such as menus and application's settings
 *
 * @package		Global
 * @version		1.0
 * @author 		Aby Dahana <abydahana@gmail.com>
 * @copyright 	Copyright (c) 2016, Aby Dahana
 * @link		https://www.facebook.com/abyprogrammer
 */

if(!function_exists('get_setting'))
{
	function get_setting($field = null)
	{
		$CI											=& get_instance();
		
		$CI->load->database();
		
		if($CI->db->field_exists($field, 'app__settings'))
		{
			$query									= $CI->db->select($field)->get('app__settings', 1);
			
			return $query->row($field);
		}
		else
		{
			return false;
		}
	}
}

if(!function_exists('get_userdata'))
{
	function get_userdata($field = null)
	{
		$CI											=& get_instance();
		$CI->load->database();
		$CI->load->library('session');
		
		if($CI->session->userdata($field))
		{
			return $CI->session->userdata($field);
		}
		elseif($CI->session->userdata('user_id') && $CI->db->field_exists($field, 'app__users'))
		{
			return $CI->db->select($field)->get_where
			(
				'app__users',
				array
				(
					'user_id'						=> $CI->session->userdata('user_id')
				),
				1
			)
			->row($field);
		}
	}
}

if(!function_exists('aksara'))
{
	function aksara($variable = null)
	{
		$CI											=& get_instance();
		$modified									= filemtime(str_replace('helpers', 'core', dirname(__FILE__)) . '/Aksara.php');
		
		if('version' == $variable)
		{
			return (date('y', $modified) - (date('y', $modified) - 2)) . '.' . date('n', $modified) . '.' . date('j', $modified);
		}
		elseif('built_version' == $variable)
		{
			return (date('y', $modified) - (date('y', $modified) - 2)) . '.' . date('n', $modified) . '.' . date('j', $modified) . CI_VERSION;
		}
		elseif('date_modified' == $variable)
		{
			return date('F d Y H:i:s', $modified);
		}
		
		return false;
	}
}

if(!function_exists('truncate'))
{
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
		$string										= strip_tags($string);
		$string										= strip_tags(str_replace(array("\r", "\n"), '', $string));
		
		if($limit && strlen($string) >= $limit)
		{
			$string									= substr($string, 0, $limit) . $pad;
		}
		
		return $string;
	}
}

if(!function_exists('is_json'))
{
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

/**
 * make_json
 * Generate the output to JSON format
 */
if(!function_exists('make_json'))
{
	function make_json($data = array(), $filename = null)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		
		$CI											=& get_instance();
		
		if(isset($data->html))
		{
			/* make a backup of "pre" tag */
			preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $data->html, $pre_backup);
			$data->html								= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $data->html);
			
			$data->html								= preg_replace(array('/[\n\t\s]+/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/(\>)\s*(\<)/m'), array(' ', '>', '<', '\\1', '$1$2'), $data->html);
			
			/* rollback the pre tag */
			$data->html								= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $data->html);
		}
		
		$output										= json_encode($data);
		
		if($filename)
		{
			$CI->output->set_header('Content-disposition: attachment; filename=' . $filename . '.json');
		}
		
		$CI->output->set_status_header(200);
		$CI->output->set_header('Content-Length: ' . strlen($output));
		$CI->output->set_content_type('application/json');
		$CI->output->set_output($output);
		$CI->output->_display();
		
		exit;
	}
}

if(!function_exists('ip_in_range'))
{
	function ip_in_range($whitelist = array())
	{
		$ip											= $_SERVER['REMOTE_ADDR'];
		
		if(in_array($ip, $whitelist))
		{
			return true;
		}
		else
		{
			foreach($whitelist as $i)
			{
				$wildcardPos						= strpos($i, '*');
				
				if($wildcardPos !== false && substr($_SERVER['REMOTE_ADDR'], 0, $wildcardPos) . '*' == $i)
				{
					return true;
				}
			}
		}

		return false;
	}
}

if(!function_exists('generate_token'))
{
	function generate_token($data = null)
	{
		if(is_array($data))
		{
			$data									= http_build_query($data);
		}
		
		return substr(sha1($data . SALT . get_userdata('session_generated')), 6, 6);
	}
}

if(!function_exists('format_slug'))
{
	function format_slug($string = null)
	{
		$string										= strtolower(preg_replace('/[\-\s]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', trim($string))));
		
		if(!preg_match('/(\d{10})/', $string))
		{
			$string									= $string;
		}
		
		return $string;
	}
}

if(!function_exists('get_filesize'))
{
	function get_filesize($file = null)
	{
		$size										= array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$bytes										= @filesize($file);
		$factor										= floor((strlen($bytes) - 1) / 3);
		
		return sprintf('%.2f', ($bytes / pow(1024, $factor))) . @$size[$factor];
	}
}

if(!function_exists('array_sort'))
{
	function make_cmp($data = array())
	{
		return function ($a, $b) use (&$data)
		{
			foreach ($data as $column => $sort)
			{
				if(!$sort)
				{
					$sort							= 'asc';
				}
				
				$diff								= strcmp((is_object($a) ? $a->$column : $a[$column]), (is_object($b) ? $b->$column : $b[$column]));
				
				if($diff !== 0)
				{
					if('asc' === strtolower($sort))
					{
						return $diff;
					}
					
					return $diff * -1;
				}
			}
			
			return 0;
		};
	}
	
	function array_sort($data = null, $order_by = array(), $sort = 'asc')
	{
		if(!is_array($order_by) && is_string($order_by))
		{
			$order_by								= array($order_by => $sort);
		}
		
		usort($data, make_cmp($order_by));
		
		return $data;
	}
}

if(!function_exists('reset_sort'))
{
	function reset_sort($resource = array())
	{
		$is_numeric									= false;
		
		foreach($resource as $key => $val)
		{
			if(is_array($val))
			{
				$resource[$key]						= reset_sort($val);
			}
			
			if(is_numeric($key))
			{
				$is_numeric							= true;
			}
		}
		
		if($is_numeric)
		{
			return array_values($resource);
		}
		else
		{
			return $resource;
		}
		
		return array_values($resource);
	}
}

if(!function_exists('in_array_r'))
{
	function in_array_r($needle = null, $haystack = array(), $strict = false)
	{
		foreach($haystack as $key => $item)
		{
			if(($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
			{
				return true;
			}
		}
		
		return false;
	}
}

if(!function_exists('array_to_xml'))
{
	function array_to_xml($array, &$xml)
	{
		foreach($array as $key => $value)
		{               
			if(is_array($value))
			{            
				if(!is_numeric($key))
				{
					$subnode						= $xml->addChild($key);
					
					array_to_xml($value, $subnode);
				}
				else
				{
					array_to_xml($value, $subnode);
				}
			}
			else
			{
				$xml->addChild($key, $value);
			}
		}        
	}
}

/**
 * throw_exception
 * Create exception message
 */
if(!function_exists('throw_exception'))
{
	function throw_exception($code = 500, $data = array(), $target = null, $redirect = false)
	{
		$CI											=& get_instance();
		
		/* check if the request isn't through xhr */
		if(!$CI->input->is_ajax_request() && $target)
		{
			/* check if data isn't an array */
			if($data && !is_array($data))
			{
				/* set the flashdata */
				if(in_array($code, array(200, 301)))
				{
					/* success */
					$CI->session->set_flashdata('success', $data);
				}
				elseif(in_array($code, array(403, 404)))
				{
					/* warning */
					$CI->session->set_flashdata('warning', $data);
				}
				else
				{
					/* unexpected error */
					$CI->session->set_flashdata('error', $data);
				}
			}
			
			/* redirect into target */
			redirect($target);
		}
		
		$exception									= array();
		
		if(is_array($data))
		{
			foreach($data as $key => $val)
			{
				$key								= str_replace('[]', null, $key);
				$exception[$key]					= $val;
			}
		}
		else
		{
			$exception								= $data;
		}
		
		/* format to json */
		$CI->output->set_content_type('application/json');
		$CI->output->set_output
		(
			json_encode
			(
				array
				(
					'status'						=> $code,
					'exception'						=> $exception,
					'target'						=> $target,
					'redirect'						=> $redirect
				)
			)
		);
		
		$CI->output->_display();
		
		exit;
	}
}

/**
 * show_flashdata
 * Pop the flashdata up
 */
if(!function_exists('show_flashdata'))
{
	function show_flashdata()
	{
		$CI											=& get_instance();
		
		if($CI->session->flashdata())
		{
			return '
				<div class="alert ' . ($CI->session->flashdata('success') ? 'alert-success' : ($CI->session->flashdata('warning') ? 'alert-warning' : 'alert-danger')) . ' alert-dismissable fade' . ($CI->session->flashdata() ? ' show' : null) . ' exception text-center rounded-0 fixed-top">
					<i class="mdi mdi-' . ($CI->session->flashdata('success') ? 'check' : ($CI->session->flashdata('warning') ? 'alert-octagram-outline' : 'emoticon-sad-outline')) . '"></i>
					' . ($CI->session->flashdata('success') ? $CI->session->flashdata('success') : ($CI->session->flashdata('warning') ? $CI->session->flashdata('warning') : $CI->session->flashdata('error'))) . '
				</div>
			';
		}
		
		return false;
	}
}

/**
 * get_announcements
 * Get active announcements
 *
 * @params		bool
 * @params		int
 * @return		string
 */
if(!function_exists('get_announcements'))
{
	function get_announcements($placement = null, $limit = null)
	{
		$CI											=& get_instance();
		
		$query										= $CI->db->order_by('announcement_id', 'desc')->get_where
		(
			'app__announcements',
			array
			(
				'status'							=> 1,
				'placement'							=> (1 == $placement ? 1 : 0),
				'start_date <= '					=> date('Y-m-d'),
				'end_date >= '						=> date('Y-m-d')
			),
			(is_numeric($limit) ? $limit : 10)
		)
		->result();
		
		$item										= null;
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$item								.= '<li><a href="' . base_url('announcements/' . $val->announcement_slug) . '" target="_blank">' . $val->title . '</a></li><li class="ticker-spacer"><img src="' . get_image('settings', get_setting('app_icon'), 'icon') . '" height="16" /></li>';
			}
		}
		
		return ($item ? '<ul role="announcements" class="announcements-ticker alias-announcements-ticker">' . $item . '</ul>' : false);
	}
}

/**
 * array_avg
 * Get the average of array
 */
if(!function_exists('array_avg'))
{
	function array_avg($array = array(), $round = 1)
	{
		if(!is_array($array)) return false;
		
		$num										= count($array);
		
		return array_map
		(
			function($val) use ($num,$round)
			{
				return array
				(
					'count'							=> $val,
					'avg'							=> round($val / $num * 100, $round)
				);
			},
			array_count_values($array)
		);
	}
}

/**
 * time_ago
 * Convert timestamp to elapsed time
 */
if(!function_exists('time_ago'))
{
	function time_ago($datetime = null, $full = false)
	{
		$now										= new DateTime;
		$ago										= new DateTime($datetime);
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

if(!function_exists('load_asset'))
{
	function load_asset($asset = null)
	{
		if($asset)
		{
			return base_url(ASSET_PATH . '/' . $asset);
		}
	}
}