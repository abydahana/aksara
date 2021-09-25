<?php namespace Aksara\Modules\Administrative\Controllers\Cleaner;
/**
 * Administrative > Cleaner
 * This module is used to clean everything that not used anymore by
 * the application (garbage).
 *
 * @author			Aby Dahana
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
		
		$this->set_permission(1);
		
		$this->set_theme('backend');
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
		$garbage_visitors							= $this->model->delete
		(
			'app__visitor_logs',
			array
			(
				'timestamp < '						=> date('Y-m-d H:i:s', strtotime('-7 days'))
			)
		);
		
		$logs_cleaned								= $this->model->affected_rows();
		
		/**
		 * Clean session garbage
		 */
		$session_driver								= service('request')->config->sessionDriver;
		$session_name								= service('request')->config->sessionCookieName;
		$session_expiration							= service('request')->config->sessionExpiration;
		$session_path								= service('request')->config->sessionSavePath;
		$session_match_ip							= service('request')->config->sessionMatchIP;
		$session_cleaned							= 0;
		
		$pattern									= sprintf('#\A%s' . ($session_match_ip ? '[0-9a-f]{32}' : '') . '\z#', preg_quote($session_name));
		
		if(stripos($session_driver, 'file') !== false)
		{
			// file session handler
			if(is_writable(WRITEPATH . $session_path))
			{
				helper('filesystem');
				
				$session							= directory_map(WRITEPATH . $session_path);
				
				if($session)
				{
					foreach($session as $key => $val)
					{
						$modified_time				= filemtime(WRITEPATH . $session_path . DIRECTORY_SEPARATOR . $val);
						
						if('index.html' == $val || !preg_match($pattern, $val) || !is_file(WRITEPATH . $session_path . DIRECTORY_SEPARATOR . $val) || !$modified_time || $modified_time > (time() - $session_expiration))
						{
							continue;
						}
						
						if(unlink(WRITEPATH . $session_path . DIRECTORY_SEPARATOR . $val))
						{
							$session_cleaned++;
						}
					}
				}
			}
			else
			{
				$error								= phrase('the_session_save_path_is_not_writable');
			}
		}
		elseif(stripos($session_driver, 'database') !== false)
		{
			// database session handler
			$query									= $this->model->delete
			(
				$session_path,
				array
				(
					'timestamp < '					=> (time() - $session_expiration)
				)
			);
			
			$session_cleaned						= $this->model->affected_rows();
		}
		
		if($error)
		{
			// throw with error
			return throw_exception(403, $error, go_to());
		}
		elseif($logs_cleaned > 0 || $session_cleaned > 0)
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
					<div class="col-6 text-right">
						' . phrase('visitor_logs') . '
					</div>
					<div class="col-6">
						' . number_format($logs_cleaned) . ' ' . phrase('cleaned') . '
					</div>
				</div>
				<div class="row">
					<div class="col-6 text-right">
						' . phrase('expired_session') . '
					</div>
					<div class="col-6">
						' . number_format($session_cleaned) . ' ' . phrase('cleaned') . '
					</div>
				</div>
				<hr class="row" />
				<div class="text-right">
					<a href="javascript:void(0)" class="btn btn-light" data-dismiss="modal">
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
