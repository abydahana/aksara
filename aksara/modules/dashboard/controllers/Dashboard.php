<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Dashboard
 * Dashboard module, can be override to the /modules/ path. Also applied for other module
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Dashboard extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_method('index');
		
		if('count_upload' == $this->input->post('request'))
		{
			return make_json($this->_directory_info(true));
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('dashboard'))
		->set_icon('mdi mdi-monitor-dashboard')
		
		->set_output
		(
			array
			(
				'writable'							=> array
				(
					'uploads'						=> (is_dir(UPLOAD_PATH) && is_writable(UPLOAD_PATH) ? true : false),
					'logs'							=> (is_dir(LOG_PATH) && is_writable(LOG_PATH) ? true : false),
					'translations'					=> (is_dir(TRANSLATION_PATH) && is_writable(TRANSLATION_PATH) ? true : false)
				),
				'visitors'							=> $this->_visitors(),
				'recent_signed'						=> $this->_recent_signed(),
				'directory'							=> $this->_directory_info(),
				'system_language'					=> $this->_system_language()
			)
		)
		
		->render();
	}
	
	private function _visitors()
	{
		$frontend_log								= array();
		
		foreach(range(0, 6) as $key => $val)
		{
			if(file_exists(LOG_PATH . '/' . ($val ? date('Y-m-d', strtotime('-' . $val . ' day')) : date('Y-m-d')) . '.json'))
			{
				$get_log							= file_get_contents(LOG_PATH . '/' . ($val ? date('Y-m-d', strtotime('-' . $val . ' day')) : date('Y-m-d')) . '.json');
				$get_log							= ($get_log ? json_decode($get_log, true) : array());
				
				foreach($get_log as $_key => $_val)
				{
					$identity						= $_val['ip_address'] . '_' . strtotime($_val['timestamp']);
					$frontend_log[$identity]		= $_val;
				}
			}
		}
		
		$backend_log								= $this->model->select
		('
			ip_address,
			browser,
			platform,
			timestamp,
			DATE(timestamp) AS date
		')
		->group_by('user_id, date')
		->get_where
		(
			'app__activity_logs',
			array
			(
				'timestamp >= '						=> date('Y-m-d H:i:s', strtotime('-7 days'))
			)
		)
		->result_array();
		
		$output										= array();
		
		foreach(range(1, 7) as $key => $val)
		{
			$date									= new DateTime();
			$date->add(new DateInterval('P' . $val . 'D'));
			
			$day									= phrase(strtolower($date->format('l')));
			$output['day'][]						= $day;
			$output['frontend'][$day]				= 0;
			$output['backend'][$day]				= 0;
		}
		
		if($frontend_log)
		{
			foreach($frontend_log as $key => $val)
			{
				$date								= phrase(date('l', strtotime($val['timestamp'])));
				
				$output['frontend'][$date]++;
			}
		}
		
		if($backend_log)
		{
			foreach($backend_log as $key => $val)
			{
				$date								= phrase(date('l', strtotime($val['timestamp'])));
				
				$output['backend'][$date]++;
			}
		}
		
		$browsers									= array_merge($frontend_log, $backend_log);
		
		$browser_log								= array
		(
			'chrome'								=> 0,
			'firefox'								=> 0,
			'safari'								=> 0,
			'edge'									=> 0,
			'opera'									=> 0,
			'explorer'								=> 0,
			'unknown'								=> 0
		);
		
		if($browsers)
		{
			foreach($browsers as $key => $val)
			{
				if(stripos($val['browser'], 'chrome') !== false)
				{
					$browser_log['chrome']++;
				}
				elseif(stripos($val['browser'], 'firefox') !== false)
				{
					$browser_log['firefox']++;
				}
				elseif(stripos($val['browser'], 'safari') !== false)
				{
					$browser_log['safari']++;
				}
				elseif(stripos($val['browser'], 'edge') !== false)
				{
					$browser_log['edge']++;
				}
				elseif(stripos($val['browser'], 'opera') !== false)
				{
					$browser_log['opera']++;
				}
				elseif(stripos($val['browser'], 'explorer') !== false)
				{
					$browser_log['explorer']++;
				}
				else
				{
					$browser_log['unknown']++;
				}
			}
			
			arsort($browser_log);
		}
		
		return array
		(
			'categories'							=> $output['day'],
			'frontend'								=> array_values($output['frontend']),
			'backend'								=> array_values($output['backend']),
			'browsers'								=> $browser_log
		);
	}
	
	private function _recent_signed()
	{
		$query										= $this->model->select
		('
			app__users.first_name,
			app__users.last_name,
			app__users.photo,
			app__groups.group_name
		')
		->join
		(
			'app__groups',
			'app__groups.group_id = app__users.group_id'
		)
		->order_by('last_login', 'DESC')
		->get_where
		(
			'app__users',
			array
			(
				'app__users.status'					=> 1
			),
			7
		)
		->result();
		
		return $query;
	}
	
	private function _directory_info($approximate = false)
	{
		$bytestotal									= 0;
		$path										= realpath($_SERVER['DOCUMENT_ROOT'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
		
		if($approximate)
		{
			$upload_path							= UPLOAD_PATH;
			if($upload_path && file_exists($upload_path))
			{
				foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_path, FilesystemIterator::SKIP_DOTS)) as $object)
				{
					$bytestotal						+= $object->getSize();
				}
			}
			
			$base									= log($bytestotal, 1024);
			$suffix									= array('', 'KB', 'MB', 'GB', 'TB');
			$bytestotal								= number_format(round(pow(1024, $base - floor($base)), 2)) . ' ' . $suffix[floor($base)];
		}
		
		return array
		(
			'path'									=> $path,
			'size'									=> $bytestotal
		);
	}
	
	private function _system_language()
	{
		
		$language_id								= get_setting('app_language');
		$query										= $this->model->select('language')->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $language_id
			)
		)
		->row('language');
		
		return ($query ? $query : phrase('default'));
	}
}
