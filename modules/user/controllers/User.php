<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * User module
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class User extends Aksara
{
	private $_tmp_table								= null;
	private $_total_rows							= 0;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_username							= ($this->uri->segment(2) ? $this->uri->segment(2) : get_userdata('username'));
		$this->_primary								= $this->model->select('user_id, first_name, last_name')->get_where('app__users', array('username' => $this->_username), 1)->row();
		
		/* check if user is exists */
		if(!$this->_primary)
		{
			return throw_exception(404, phrase('user_was_not_found_or_account_is_temporary_blocked'), base_url());
		}
		
		$this->_title								= $this->_primary->first_name . ' ' . $this->_primary->last_name;
		$this->_primary								= $this->_primary->user_id;
		
		$this->_tmp_table							= 'tmp__' . $this->_username;
		$this->_limit								= 25;
		$this->_offset								= ($this->input->get('per_page') > 1 ? ($this->input->get('per_page') * $this->_limit) - $this->_limit : 0);
	}
	
	public function index()
	{
		$this->set_title($this->_title)
		->set_icon('mdi mdi-account')
		->set_output
		(
			array
			(
				/* get userdata */
				'userdata'							=> $this->model->select
				('
					app__users.username,
					app__users.first_name,
					app__users.last_name,
					app__users.photo,
					app__users.address,
					app__users.bio,
					app__users.email,
					app__users.registered_date,
					app__groups.group_name
				')
				->join
				(
					'app__groups',
					'app__groups.group_id = app__users.group_id'
				)
				->get_where
				(
					'app__users',
					array
					(
						'app__users.user_id'		=> $this->_primary
					),
					1
				)
				->row(),
				
				/* get timeline */
				'timeline'							=> $this->_timeline(),
				
				/* set pagination property */
				'pagination'						=> array
				(
					'per_page'						=> $this->_limit,
					'offset'						=> $this->_offset,
					'total_rows'					=> $this->_total_rows
				)
			)
		)
		->render();
	}
	
	private function _timeline()
	{
		return false;
		/* drop temporary table if exists */
		$this->model->query('DROP TABLE IF EXISTS ' . $this->_tmp_table);
		
		/* create temporary table */
		$this->model->query
		('
			CREATE TEMPORARY TABLE ' . $this->_tmp_table . '
			(
				id int(11) auto_increment,
				
				item_id int(11),
				slug varchar(128),
				description text,
				timestamp datetime,
				
				user_id int(11),
				
				is_blog tinyint(1),
				is_blog_reply tinyint(1),
				is_signmark tinyint(1),
				is_signmark_reply tinyint(1),
				is_signmark_like tinyint(1),
				
				PRIMARY KEY(id)
			)
		');
		
		/* prepare data */
		$prepare									= $this->model->select
		('
			post_id AS item_id,
			post_title AS description,
			post_slug AS slug,
			created_timestamp AS timestamp,
			author AS user_id,
			1 AS is_blog
		')
		->get_where
		(
			'blogs',
			array
			(
				'author'							=> $this->_primary
			)
		)
		->result_array();
		
		/* insert results into the temporary table */
		if($prepare)
		{
			$this->model->insert_batch($this->_tmp_table, $prepare, sizeof($prepare));
		}
		
		/* prepare data */
		$prepare									= $this->model->select
		('
			id AS item_id,
			comment AS description,
			blogs.post_slug AS slug,
			timestamp,
			user_id,
			1 AS is_blog_reply
		')
		->join
		(
			'blogs',
			'blogs.post_id = blogs__replies.post_id'
		)
		->get_where
		(
			'blogs__replies',
			array
			(
				'user_id'							=> $this->_primary
			)
		)
		->result_array();
		
		/* insert results into the temporary table */
		if($prepare)
		{
			$this->model->insert_batch($this->_tmp_table, $prepare, sizeof($prepare));
		}
		
		/* prepare data */
		$prepare									= $this->model->select
		('
			id AS item_id,
			sign_mark AS description,
			timestamp,
			user_id,
			1 AS is_signmark
		')
		->get_where
		(
			'witnesses',
			array
			(
				'user_id'							=> $this->_primary
			)
		)
		->result_array();
		
		/* insert results into the temporary table */
		if($prepare)
		{
			$this->model->insert_batch($this->_tmp_table, $prepare, sizeof($prepare));
		}
		
		/* prepare data */
		$prepare								= $this->model->select
		('
			id AS item_id,
			comment AS description,
			timestamp,
			user_id,
			1 AS is_signmark_reply
		')
		->get_where
		(
			'witnesses__replies',
			array
			(
				'user_id'							=> $this->_primary
			)
		)
		->result_array();
		
		/* insert results into the temporary table */
		if($prepare)
		{
			$this->model->insert_batch($this->_tmp_table, $prepare, sizeof($prepare));
		}
		
		/* prepare data */
		$prepare									= $this->model->select
		('
			id AS item_id,
			timestamp,
			user_id,
			1 AS is_signmark_like
		')
		->get_where
		(
			'witnesses__likes',
			array
			(
				'user_id'							=> $this->_primary
			)
		)
		->result_array();
		
		/* insert results into the temporary table */
		if($prepare)
		{
			$this->model->insert_batch($this->_tmp_table, $prepare, sizeof($prepare));
		}
		
		/**
		 * final action
		 * getting the data from the temporary table
		 */
		$query										= $this->model->select
		('
			app__users.username,
			app__users.first_name,
			app__users.last_name,
			app__users.photo,
			' . $this->_tmp_table . '.*
		')
		->join
		(
			$this->_tmp_table,
			$this->_tmp_table . '.user_id = app__users.user_id'
		)
		->order_by('timestamp', 'DESC')
		->group_by('item_id, is_blog, is_signmark')
		->limit($this->_limit, $this->_offset)
		->get_where
		(
			'app__users',
			array
			(
				'app__users.user_id'				=> $this->_primary
			)
		)
		->result();
		
		/* get total items */
		$this->_total_rows							= $this->model->group_by('item_id, is_blog, is_signmark')->count_all_results($this->_tmp_table);
		
		/* finally, drop temporary table if exists */
		$this->model->query('DROP TABLE IF EXISTS ' . $this->_tmp_table);
		
		return $query;
	}
}