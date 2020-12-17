<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rest API Library
 *
 * @version		2.1.1
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Rest
{
	public $_api_key;
	
	private $_status								= 200;
	private $_messages								= null;
	private $_results								= array();
	
	public function __construct()
	{
		$this->_ci									=& get_instance();
		
		$this->_ci->_limit							= ($this->_ci->input->get('limit') ? $this->_ci->input->get('limit') : 25);
		$this->_ci->_offset							= ($this->_ci->input->get('offset') ? $this->_ci->input->get('offset') : null);
		$this->_api_key								= substr($this->_ci->input->get_request_header('Authorization'), strrpos($this->_ci->input->get_request_header('Authorization'), ' ') + 1);
		$this->_client_ip							= $this->_ci->input->server('REMOTE_ADDR');
		
		$this->validate();
	}
	
	/**
	 * validate
	 *
	 * validate the request before returning the request
	 *
	 * @return		mixed
	 */
	public function validate()
	{
		/* validate api key and secret key including the valid time */
		$this->_client_api							= $this->_ci->model->get_where
		(
			'rest__clients',
			array
			(
				'api_key'							=> $this->_api_key,
				'TIME(valid_until) <= '				=> date('Y-m-d')
			)
		)
		->row();
		
		/* check the result */
		if(!$this->_client_api)
		{
			$this->_status							= 403;
			$this->_messages						= phrase('the_api_key_you_use_is_not_valid_or_already_expired');
			
			$this->_ci->output->set_status_header($this->_status)
			->set_content_type('application/json')
			->set_header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT')
			->set_header('Cache-Control: no-store, no-cache, must-revalidate')
			->set_header('Pragma: no-cache')
			->set_output
			(
				json_encode
				(
					array
					(
						'status'					=> $this->_status,
						'messages'					=> $this->_messages,
						'remote_address'			=> $this->_client_ip,
						'request_time'				=> date('Y-m-d H:i:s'),
						'results'					=> $this->_results
					)
				)
			)
			->_display();
			exit;
		}
		elseif($this->_client_api->ip_range && !$this->_ip_in_range($this->_client_ip))
		{
			/* result is found, but how about client ip? */
			$this->_status							= 403;
			$this->_messages						= phrase('you_are_not_allowed_to_access_the_page');
			
			$this->_ci->output->set_status_header($this->_status)
			->set_content_type('application/json')
			->set_header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT')
			->set_header('Cache-Control: no-store, no-cache, must-revalidate')
			->set_header('Pragma: no-cache')
			->set_output
			(
				json_encode
				(
					array
					(
						'status'					=> $this->_status,
						'messages'					=> $this->_messages,
						'remote_address'			=> $this->_client_ip,
						'request_time'				=> date('Y-m-d H:i:s'),
						'results'					=> $this->_results
					)
				)
			)
			->_display();
			exit;
		}
	}
	
	/**
	 * set_output
	 *
	 * returning the callback data into the request
	 *
	 * @param		array		$output
	 * @return		mixed
	 */
	public function set_output($status = 0, $messages = null, $results = array())
	{
		$this->_status								= $status;
		$this->_messages							= $messages;
		$this->_results								= $results;
		
		if('xml' == $this->_ci->input->get('format'))
		{
			// Under construction
		}
		else
		{
			$this->_ci->output->set_status_header($this->_status)
			->set_content_type('application/json')
			->set_header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT')
			->set_header('Cache-Control: no-store, no-cache, must-revalidate')
			->set_header('Pragma: no-cache')
			->set_output
			(
				json_encode
				(
					array
					(
						'status'					=> $this->_status,
						'messages'					=> $this->_messages,
						'remote_address'			=> $this->_client_ip,
						'request_time'				=> date('Y-m-d H:i:s'),
						'results'					=> $this->_results
					)
				)
			)
			->_display();
			exit;
		}
	}
	
	private function _ip_in_range($whitelist = array())
	{
		if($whitelist && !is_array($whitelist))
		{
			$whitelist								= array_map('trim', explode(',', $whitelist));
		}
		
		if(in_array($this->_client_ip, $whitelist))
		{
			return true;
		}
		else
		{
			foreach($whitelist as $key => $val)
			{
				$wildcardPos						= strpos($val, '*');
				
				if($wildcardPos !== false && substr($this->_client_ip, 0, $wildcardPos) . '*' == $val)
				{
					return true;
				}
			}
		}

		return false;
	}
}