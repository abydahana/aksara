<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Add or update user skill
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Skill extends Aksara
{
	private $_table									= 'users__skills';
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		
		$this->permission->must_ajax();
		$this->load->library('form_validation');
		$this->load->helper('security');
		
		$this->_primary								= $this->input->get('id');
	}
	
	public function index()
	{
		/* validate input */
		$this->form_validation->set_rules('value', phrase('skill_name'), 'required|xss_clean|callback_validate_input');
		$this->form_validation->set_rules('description', phrase('description'), 'required|xss_clean');
		$this->form_validation->set_rules('level_achieved', phrase('level_achieved'), 'required|in_list[1,2,3,4]');
		
		/* input isn't valid */
		if($this->form_validation->run() === false)
		{
			/* throw exception */
			return throw_exception(400, $this->form_validation->error_array());
		}
		
		/* prepare data */
		$prepare									= array
		(
			'user_id'								=> get_userdata('user_id'),
			'value'									=> $this->input->post('value'),
			'description'							=> $this->input->post('description'),
			'level_achieved'						=> $this->input->post('level_achieved')
		);
		
		/* check if it being update or insert */
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'user_id'							=> get_userdata('user_id'),
				'id'								=> $this->_primary,
				'value'								=> $this->input->post('value')
			),
			1
		)
		->row();
		
		if($query)
		{
			/* update existing */
			$execute								= $this->model->update($this->_table, $prepare, array('user_id' => get_userdata('user_id'), 'id' => $this->_primary), 1);
			$id										= $this->_primary;
		}
		else
		{
			/* insert new data */
			$execute								= $this->model->insert($this->_table, $prepare);
			$id										= $this->model->insert_id();
		}
		
		if($execute)
		{
			return make_json
			(
				array
				(
					'status'						=> 200,
					'template'						=> 'skill',
					'override'						=> ($this->_primary ? '#skill_' . $this->_primary : null),
					'action_href'					=> base_url('user/account/skill', array('id' => $id)),
					'delete_href'					=> base_url('user/account/skill', array('id' => $id, 'delete' => true)),
					'value'							=> $this->input->post('value'),
					'description'					=> $this->input->post('description'),
					'level_achieved'				=> $this->input->post('level_achieved')
				)
			);
		}
		
		/* there's something wrong that data cannot be executed */
		return throw_exception(500, phrase('unable_to_submit_your_data') . ' ' . phrase('please_try_again_or_contact_the_system_administrator'));
	}
	
	/**
	 * Validate input
	 */
	public function validate_input($value = null)
	{
		if($this->_primary)
		{
			$checker								= $this->model->get_where
			(
				$this->_table,
				array
				(
					'id != '						=> $this->_primary,
					'value'							=> $value,
					'user_id'						=> get_userdata('user_id')
				),
				1
			)
			->row();
		}
		else
		{
			$checker								= $this->model->get_where
			(
				$this->_table,
				array
				(
					'value'							=> $value,
					'user_id'						=> get_userdata('user_id')
				),
				1
			)
			->row();
		}
		
		$max										= $this->model->where
		(
			array
			(
				'id != '							=> $this->_primary,
				'user_id'							=> get_userdata('user_id')
			)
		)
		->count_all_results($this->_table);
		
		if($checker)
		{
			$this->form_validation->set_message('validate_input', phrase('the_skill_you_would_to_add_is_already_exists'));
			return false;
		}
		elseif($max >= 15)
		{
			$this->form_validation->set_message('validate_input', phrase('unfortunately_you_are_reached_the_maximum_quota_of_skill_storage'));
			return false;
		}
		
		return true;
	}
}