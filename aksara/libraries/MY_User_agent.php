<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MY_User_agent extends CI_User_agent
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Override Is Mobile
	 *
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_mobile($key = NULL)
	{
		if ( ! $this->is_mobile || (bool) strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ipad'))
		{
			return FALSE;
		}

		// No need to be specific, it's a mobile
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot
		return (isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key]);
	}
}