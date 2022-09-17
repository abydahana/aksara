<?php

namespace Aksara\Modules\Administrative\Controllers\Logs;

/**
 * Administrative > Logs > Errors
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Errors extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('error_logs'))
		->set_icon('mdi mdi-bug')
		
		->set_output
		(
			array
			(
				'logs'								=> $this->_logs(),
				'report'							=> $this->_report()
			)
		)
		
		->render();
	}
	
	public function remove()
	{
		$this->permission->must_ajax();
		
		if(!service('request')->getPost('log'))
		{
			$html									= '
				<form action="' . current_page() . '" method="POST" class="--validate-form">
					<div class="text-center mb-3">
						' . phrase('are_you_sure_want_to_remove_this_log') . '
					</div>
					<input type="hidden" name="log" value="' . service('request')->getGet('log') . '" />
					<hr class="row" />
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<button type="button" class="btn btn-light" data-bs-dismiss="modal">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</button>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-danger">
									<i class="mdi mdi-check"></i>
									' . phrase('continue') . '
								</button>
							</div>
						</div>
					</div>
				</form>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('remove_log'),
						'icon'						=> 'mdi mdi-alert',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		$filename									= basename(service('request')->getPost('log'));
		
		try
		{
			unlink(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename);
			
			$deleted								= true;
		}
		catch(\Throwable $e)
		{
			$deleted								= false;
		}
		
		if($deleted)
		{
			return throw_exception(301, phrase('the_selected_log_was_successfully_removed'), current_page('../', array('log' => null)));
		}
		
		return throw_exception(403, phrase('unable_to_remove_the_selected_log'), current_page('../', array('log' => null)));
	}
	
	public function clear()
	{
		$this->permission->must_ajax();
		
		if(!service('request')->getPost('confirm'))
		{
			$html									= '
				<form action="' . current_page() . '" method="POST" class="--validate-form">
					<div class="text-center mb-3">
						<i class="mdi mdi-help-rhombus-outline mdi-5x text-danger"></i>
						<br />
						<h5>
							' . phrase('are_you_sure_want_to_clear_the_logs') . '
						</h5>
						<p class="lead">
							' . phrase('this_action_will_remove_everything_related_to_the_error_logs') . '
						</p>
					</div>
					<input type="hidden" name="confirm" value="1" />
					<hr class="row" />
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<button type="button" class="btn btn-light" data-bs-dismiss="modal">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</button>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-danger">
									<i class="mdi mdi-check"></i>
									' . phrase('clear_logs') . '
								</button>
							</div>
						</div>
					</div>
				</form>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('clear_logs'),
						'icon'						=> 'mdi mdi-alert',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		/* load required helper */
		helper('filesystem');
		
		$data										= directory_map(WRITEPATH . 'logs', 1);
		$logs										= array();
		$success									= 0;
		
		if($data)
		{
			foreach($data as $key => $val)
			{
				if(pathinfo($val, PATHINFO_EXTENSION) != 'log') continue;
				
				$logs[]								= $val;
			}
		}
		
		if($logs)
		{
			foreach($logs as $key => $val)
			{
				try
				{
					unlink(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $val);
					
					$success++;
				}
				catch(\Throwable $e)
				{
				}
			}
		}
		
		return throw_exception(301, '<b>' . number_format($success) . '</b> ' . phrase('of') . ' <b>' . number_format(sizeof($logs)) . '</b> ' . phrase('was_successfully_removed'), current_page('../', array('log' => null)));
	}
	
	private function _logs()
	{
		/* load required helper */
		helper('filesystem');
		
		$data										= directory_map(WRITEPATH . 'logs', 1);
		$logs										= array();
		
		if($data)
		{
			foreach($data as $key => $val)
			{
				if(pathinfo($val, PATHINFO_EXTENSION) != 'log') continue;
				
				$logs[]								= $val;
			}
		}
		
		krsort($logs);
		
		return $logs;
	}
	
	private function _report()
	{
		if(!service('request')->getGet('report')) return false;
		
		$filename									= basename(service('request')->getGet('report'));
		
		if(file_exists(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename) && is_readable(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename))
		{
			$report									= file_get_contents(WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename);
			
			if($report)
			{
				$report								= str_replace('CodeIgniter->run()', 'CodeIgniter->run()_%_EXPLODE_ME_%_', str_replace('\\', '/', $report));
				$report								= explode('_%_EXPLODE_ME_%_', $report);
			}
			
			krsort($report);
			
			return $report;
		}
		
		return false;
	}
}
