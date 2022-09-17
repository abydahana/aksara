<?php

namespace Aksara\Modules\Administrative\Controllers\Logs;

/**
 * Administrative > Logs > Activities
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

ini_set('memory_limit', -1);

class Activities extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__activity_logs';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		if(service('request')->getGet('user_id'))
		{
			redirect(base_url('administrative/users/read?user_id=' . service('request')->getGet('user_id')));
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('activity_logs'))
		->set_icon('mdi mdi-information-outline')
		->set_primary('id')
		->unset_column('id, user_id, browser')
		->unset_view('id, user_id')
		->add_action('toolbar', 'truncate', phrase('clear_logs'), 'btn-primary --open-delete-confirm', 'mdi mdi-delete-empty')
		->set_field('timestamp', 'current_timestamp')
		->set_field('first_name', 'hyperlink', 'user', array('user_id' => 'user_id'), true)
		->set_field('path', 'custom_format', '<a href="' . base_url('{path}') . '" class="--xhr" data-bs-toggle="tooltip" title="{path}"><b><i class="mdi mdi-open-in-new"></i> ' . phrase('click_to_open') . '</b></a>')
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
				'path'								=> phrase('path'),
				'method'							=> phrase('method'),
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
		if(DEMO_MODE)
		{
			return throw_exception(403, phrase('changes_will_not_saved_in_demo_mode'), go_to());
		}
		
		if($this->model->truncate($this->_table))
		{
			return throw_exception(301, phrase('the_log_activities_was_successfully_cleaned'), go_to(null, array('per_page' => null)));
		}
		else
		{
			return throw_exception(500, phrase('unable_to_clear_the_activity_logs'), go_to());
		}
	}
}
