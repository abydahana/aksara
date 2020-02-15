<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Log Activities
 * This module used to manage the log activities created by system.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
ini_set('memory_limit', -1);
class Activities extends Aksara
{
	private $_table									= 'app__activity_logs';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		
		if(null != $this->input->get('user_id'))
		{
			redirect(base_url('administrative/users/read?user_id=' . $this->input->get('user_id')));
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('activity_logs'))
		->set_icon('mdi mdi-information-outline')
		->set_primary('id')
		->unset_column('id, user_id, module, submodule')
		->unset_view('id, user_id')
		->add_action('toolbar', 'truncate', phrase('clear_logs'), 'btn-primary --open-delete-confirm', 'mdi mdi-delete-empty')
		->set_field('timestamp', 'current_timestamp')
		->set_field('first_name', 'hyperlink', 'administrative/users/read', array('user_id' => 'user_id'))
		->merge_content('{controller}', phrase('module'))
		->set_field('page', 'custom_format', '<a href="' . base_url('{page}') . '" class="--xhr"><b>{page}</b></a>')
		->unset_action('create, update')
		->column_order('first_name')
		->view_order('first_name')
		->select('app__users.first_name')
		->join('app__users', 'app__users.user_id = ' . $this->_table . '.user_id')
		->order_by('id', 'desc')
		->set_alias
		(
			array
			(
				'module'							=> phrase('module'),
				'submodule'							=> phrase('submodule'),
				'controller'						=> phrase('controller'),
				'method'							=> phrase('method'),
				'page'								=> phrase('page'),
				'browser'							=> phrase('browser'),
				'platform'							=> phrase('platform'),
				'ip_address'						=> phrase('ip_address'),
				'timestamp'							=> phrase('access_time')
			)
		)
		->merge_content('{first_name} {last_name}', phrase('full_name'))
		->render($this->_table);
	}
	
	public function truncate()
	{
		if(defined('DEMO_MODE') && DEMO_MODE)
		{
			return throw_exception(301, phrase('changes_will_not_saved_in_demo_mode'), go_to());
		}
		
		if($this->model->truncate($this->_table))
		{
			return throw_exception(301, phrase('log_activities_was_successfully_cleared'), go_to());
		}
		else
		{
			return throw_exception(500, phrase('unable_to_clear_activity_logs'), go_to());
		}
	}
}