<?php

namespace Aksara\Modules\Administrative\Controllers\Cleaner;

/**
 * Administrative > Cleaner
 * This module is used to clean everything that not used anymore by
 * the application (garbage).
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Cleaner extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		
		$this->set_theme('backend');
		
		$this->searchable(false);
	}
	
	public function index()
	{
		$this->set_title(phrase('session_garbage_cleaner'))
		->set_icon('mdi mdi-trash-can')
		
		->render();
	}
	
	/**
	 * Clean unused session
	 */
	public function clean()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page('../'));
		}
		
		$error										= false;
		
		/**
		 * Clean visitor log garbage that exceed 7 days
		 */
		if('Postgre' == DB_DRIVER)
		{
			$this->model->where('extract(epoch from timestamp) < ', strtotime('-7 day'));
		}
		else
		{
			$this->model->where('timestamp < ', date('Y-m-d H:i:s', strtotime('-7 days')));
		}
		
		$garbage_visitors							= $this->model->delete('app__visitor_logs');
		
		$logs_cleaned								= $this->model->affected_rows();
		
		/**
		 * Clean session garbage
		 */
		$session_driver								= (service('request')->config->sessionDriver ? service('request')->config->sessionDriver : '');
		$session_name								= service('request')->config->sessionCookieName;
		$session_expiration							= service('request')->config->sessionExpiration;
		$session_path								= service('request')->config->sessionSavePath;
		$session_match_ip							= service('request')->config->sessionMatchIP;
		$session_cleaned							= 0;
		
		if(stripos($session_driver, 'file') !== false)
		{
			// file session handler
			if(is_writable($session_path))
			{
				helper('filesystem');
				
				$session							= directory_map($session_path);
				
				if($session)
				{
					foreach($session as $key => $val)
					{
						$modified_time				= filemtime($session_path . DIRECTORY_SEPARATOR . $val);
						
						if('index.html' == $val || !is_file($session_path . DIRECTORY_SEPARATOR . $val) || !$modified_time || $modified_time > (time() - $session_expiration))
						{
							continue;
						}
						
						try
						{
							if(unlink($session_path . DIRECTORY_SEPARATOR . $val))
							{
								$session_cleaned++;
							}
						}
						catch(\Throwable $e)
						{
						}
					}
				}
			}
			else
			{
				$error								= phrase('the_session_save_path_is_not_writable');
			}
		}
		else if(stripos($session_driver, 'database') !== false)
		{
			// database session handler
			if('Postgre' == DB_DRIVER)
			{
				$this->model->where('extract(epoch from timestamp) < ', (time() - $session_expiration));
			}
			else
			{
				$this->model->where('timestamp < ', (time() - $session_expiration));
			}
			
			$query									= $this->model->delete($session_path);
			
			$session_cleaned						= $this->model->affected_rows();
		}
		
		$error_log									= directory_map(WRITEPATH . 'logs');
		
		if($error_log)
		{
			foreach($error_log as $key => $val)
			{
				if(!is_array($val) && pathinfo($val, PATHINFO_EXTENSION) != 'html')
				{
					@unlink(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $val);
				}
			}
		}
		
		if($error)
		{
			// throw with error
			return throw_exception(403, $error, go_to());
		}
		else if($logs_cleaned > 0 || $session_cleaned > 0)
		{
			// throw with amount of cleaned garbage
			$html									= '
				<div class="text-center">
					<i class="mdi mdi-delete-empty mdi-5x text-success"></i>
					<br />
					<h5>
						' . phrase('the_session_garbage_was_successfully_cleaned') . '
					</h5>
				</div>
				<p class="text-center">
					' . phrase('below_is_the_detailed_information_about_the_cleaned_garbage') . '
				</p>
				<div class="row">
					<div class="col-6 text-end">
						' . phrase('visitor_logs') . '
					</div>
					<div class="col-6">
						<b>' . number_format($logs_cleaned) . '</b> ' . phrase('cleaned') . '
					</div>
				</div>
				<div class="row">
					<div class="col-6 text-end">
						' . phrase('expired_session') . '
					</div>
					<div class="col-6">
						<b>' . number_format($session_cleaned) . '</b> ' . phrase('cleaned') . '
					</div>
				</div>
				<hr class="border-secondary" />
				<div class="text-end">
					<a href="javascript:void(0)" class="btn btn-light" data-bs-dismiss="modal">
						<i class="mdi mdi-window-close"></i>
						' . phrase('close') . '
						<em class="text-sm">(esc)</em>
					</a>
				</div>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('garbage_cleaned'),
						'icon'						=> 'mdi mdi-check',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		// no garbage found
		return throw_exception(301, phrase('there_are_no_session_garbage_available_right_now'), go_to());
	}
}
