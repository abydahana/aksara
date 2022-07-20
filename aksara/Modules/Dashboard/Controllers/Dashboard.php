<?php

namespace Aksara\Modules\Dashboard\Controllers;

/**
 * Dashboard
 * Dashboard module, can be override to the /modules/ path. Also applied for other module
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Dashboard extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_method('index');
		
		if('fetch_information' == service('request')->getPost('request'))
		{
			return $this->_fetch_information();
		}
	}
	
	public function index()
	{
		if(get_userdata('group_id') > 2)
		{
			$this->set_template('index', 'index_subscriber');
		}
		else
		{
			$this->set_output
			(
				array
				(
					'card'							=> $this->_card(),
					'permission'					=> array
					(
						'uploads'					=> (is_dir(FCPATH . UPLOAD_PATH) && is_writable(FCPATH . UPLOAD_PATH) ? true : false),
						'writable'					=> (is_dir(WRITEPATH) && is_writable(WRITEPATH) ? true : false),
					),
					'visitors'						=> $this->_visitors(),
					'recent_signed'					=> $this->_recent_signed(),
					'system_language'				=> $this->_system_language(),
					'announcements'					=> $this->_announcements()
				)
			);
		}
		
		$this->set_title(phrase('dashboard'))
		->set_icon('mdi mdi-monitor-dashboard')
		
		->render();
	}
	
	private function _card()
	{
		$blogs										= $this->model->get_where
		(
			'blogs',
			array
			(
			)
		)
		->count_all_results();
		
		$pages										= $this->model->get_where
		(
			'pages',
			array
			(
			)
		)
		->count_all_results();
		
		$galleries									= $this->model->get_where
		(
			'galleries',
			array
			(
			)
		)
		->count_all_results();
		
		$users										= $this->model->get_where
		(
			'app__users',
			array
			(
			)
		)
		->count_all_results();
		
		return array
		(
			'blogs'									=> ($blogs ? $blogs : 0),
			'pages'									=> ($pages ? $pages : 0),
			'galleries'								=> ($galleries ? $galleries : 0),
			'users'									=> ($users ? $users : 0)
		);
	}
	
	private function _visitors()
	{
		$visitors									= $this->model->select
		('
			ip_address,
			browser,
			platform,
			timestamp
		')
		->group_by('ip_address, browser, platform, timestamp, ' . (in_array(DB_DRIVER, array('Postgre', 'SQLSRV')) ? 'CAST(timestamp AS DATE)' : 'DATE(timestamp)'))
		->get_where
		(
			'app__visitor_logs',
			array
			(
				'timestamp > '						=> date('Y-m-d', strtotime('-6 days')) . ' 00:00:00',
				'timestamp < '						=> date('Y-m-d H:i:s')
			)
		)
		->result();
		
		$output										= array();
		
		foreach(range(1, 7) as $key => $val)
		{
			$date									= new \DateTime();
			$date->add(new \DateInterval('P' . $val . 'D'));
			
			$day									= phrase(strtolower($date->format('l')));
			$output['days'][]						= $day;
			$output['visits'][$day]					= 0;
		}
		
		$browsers									= array
		(
			'chrome'								=> 0,
			'firefox'								=> 0,
			'safari'								=> 0,
			'edge'									=> 0,
			'opera'									=> 0,
			'explorer'								=> 0,
			'unknown'								=> 0
		);
		
		if($visitors)
		{
			foreach($visitors as $key => $val)
			{
				$date								= phrase(date('l', strtotime($val->timestamp)));
				
				$output['visits'][$date]++;
				
				if(stripos($val->browser, 'chrome') !== false)
				{
					$browsers['chrome']++;
				}
				else if(stripos($val->browser, 'firefox') !== false)
				{
					$browsers['firefox']++;
				}
				else if(stripos($val->browser, 'safari') !== false)
				{
					$browsers['safari']++;
				}
				else if(stripos($val->browser, 'edge') !== false)
				{
					$browsers['edge']++;
				}
				else if(stripos($val->browser, 'opera') !== false)
				{
					$browsers['opera']++;
				}
				else if(stripos($val->browser, 'explorer') !== false)
				{
					$browsers['explorer']++;
				}
				else
				{
					$browsers['unknown']++;
				}
			}
		}
		
		arsort($browsers);
		
		return array
		(
			'categories'							=> $output['days'],
			'visits'								=> array_values($output['visits']),
			'browsers'								=> $browsers
		);
	}
	
	private function _recent_signed()
	{
		$query										= $this->model->select
		('
			app__users.user_id,
			app__users.username,
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
	
	private function _announcements()
	{
		$query										= $this->model->order_by('end_date', 'DESC')
		->order_by('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		->get_where
		(
			'app__announcements',
			array
			(
				'placement'							=> 0,
				'status'							=> 1,
				'start_date <= '					=> date('Y-m-d'),
				'end_date >= '						=> date('Y-m-d')
			),
			5
		)
		->result();
		
		return $query;
	}
	
	private function _fetch_information()
	{
		$updater									= false;
		
		if(get_setting('update_check'))
		{
			$updater								= \Aksara\Modules\Administrative\Controllers\Updater\Updater::ping_upstream();
		}
		
		return make_json
		(
			array
			(
				'update_available'					=> $updater
			)
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
