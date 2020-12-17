<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Additional tweaks of form_validation
 */
class MY_Form_validation extends CI_Form_validation
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Check if data is already exist in the database table
	 */
	public function is_unique($value = null, $params = null)
	{
		$params										= explode('.', $params);
		if($params)
		{
			$sliced									= array_slice($params, 2, sizeof($params));
			$odd									= array();
			$even									= array();
			foreach($sliced as $key => $val)
			{
				if($key % 2 == 0)
				{
					$even[]						 	= $val;
				}
				else
				{
					$odd[]							= $val;
				}
			}
			
			$sliced									= array_combine($even, $odd);
			$num									= 0;
			foreach($sliced as $key => $val)
			{
				if(!$num)
				{
					$this->CI->db->where($key . ' != ', $val);
				}
				else
				{
					$this->CI->db->where($key, $val);
				}
				$num++;
			}
			return $this->CI->db->select($params[1])->get_where($params[0], array($params[1] => $value), 1)->num_rows() === 0;
		}
		
		return false;
	}
	
	/**
	 * Check if field is valid boolean
	 */
	public function is_boolean($value = null)
	{
		if(null != $value && 1 != $value)
		{
			$this->CI->form_validation->set_message('is_boolean', phrase('the_field') . ' %s ' . phrase('is_not_a_valid_boolean'));
			return false;
		}
		return true;
	}
	
	/**
	 * Check if field is valid currency
	 */
	public function is_currency($value = null)
	{
		if(!preg_match('/^\s*[$]?\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/', $value))
		{
			$this->CI->form_validation->set_message('is_currency', '%s: ' . phrase('the_field_must_contain_a_valid_currency'));
			return false;
		}
		return true;
	}
	
	/**
	 * Check if field is valid hex
	 */
	public function valid_hex($value = null)
	{
		if(!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value))
		{
			$this->CI->form_validation->set_message('valid_hex', phrase('the_field_%s_must_be_valid_hex_color_code'));
			return false;
		}
		return true;
	}
	
	/**
	 * Check if field is valid date
	 */
	public function valid_date($value = null)
	{
		$valid_date									= DateTime::createFromFormat('Y-m-d', $value);
		if(!$valid_date || $valid_date && $valid_date->format('Y-m-d') !== $value)
		{
			$this->CI->form_validation->set_message('valid_date', phrase('the_field') . ' %s ' . phrase('is_not_a_valid_date'));
			return false;
		}
		return true;
	}
	
	/**
	 * Check if field is valid year
	 */
	public function valid_year($value = null)
	{
		$valid_year									= range(1970, date('Y'));
		if(!in_array($value, $valid_year))
		{
			$this->CI->form_validation->set_message('valid_year', phrase('the_field') . ' %s ' . phrase('is_not_a_valid_year'));
			return false;
		}
		return true;
	}
	
	/**
	 * Check relation table
	 */
	public function relation_checker($value = 0, $params = null)
	{
		list($table, $field)						= array_pad(explode('.', $params), 2, null);
		
		/* check table existence */
		if(!$this->CI->db->table_exists($table))
		{
			$this->CI->form_validation->set_message('relation_checker', phrase('the_relation_table_does_not_exist'));
			return false;
		}
		
		/* check field existence */
		elseif(!$this->CI->db->field_exists($field, $table))
		{
			$this->CI->form_validation->set_message('relation_checker', phrase('the_field_for_this_relation_table_does_not_exist'));
			return false;
		}
		
		/* check if relation data is exists */
		elseif(!$this->CI->db->select($field)->get_where($table, array($field => $value))->row($field))
		{
			$this->CI->form_validation->set_message('relation_checker', phrase('the_selected_data_for_this_relation_does_not_exist'));
			return false;
		}
		
		return true;
	}
}