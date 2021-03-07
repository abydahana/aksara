<?php namespace Aksara\Modules\Cms\Controllers\Partials;
/**
 * CMS > Partials > Announcements
 * Manage announcements.
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Announcements extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__announcements';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('announcements'))
		->set_icon('mdi mdi-bullhorn-outline')
		->set_primary('announcement_id')
		->unset_column('announcement_id, content, created_timestamp, updated_timestamp, announcement_slug, language')
		->unset_field('announcement_id')
		->unset_view('announcement_id')
		->set_field
		(
			array
			(
				'content'							=> 'wysiwyg',
				'start_date'						=> 'datepicker',
				'end_date'							=> 'datepicker',
				'created_timestamp'					=> 'current_timestamp',
				'updated_timestamp'					=> 'current_timestamp',
				'status'							=> 'boolean'
			)
		)
		->set_field
		(
			'placement',
			'radio',
			array
			(
				0									=> phrase('front_end'),
				1									=> phrase('back_end')
			)
		)
		->set_field('announcement_slug', 'to_slug', 'title')
		->set_field('announcement_title', 'hyperlink', 'announcements', array('announcement_slug' => 'announcement_slug'), true)
		
		->add_action('option', '../../../announcements/get', phrase('view_announcement'), 'btn-success', 'mdi mdi-eye', array('announcement_slug' => 'announcement_slug'), true)
		
		->add_class('content', 'minimal')
		->merge_field('start_date, end_date')
		->set_relation
		(
			'language_id',
			'app__languages.id',
			'{app__languages.language}',
			array
			(
				'app__languages.status'				=> 1
			)
		)
		->set_validation
		(
			array
			(
				'title'								=> 'required|max_length[256]|unique[' . $this->_table . '.title.announcement_id.' . service('request')->getGet('announcement_id') . ']',
				'content'							=> 'required',
				'language_id'						=> 'required',
				'start_date'						=> 'required',
				'end_date'							=> 'required|callback_validate_end_date',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'title'								=> phrase('title'),
				'announcement_slug'					=> phrase('slug'),
				'content'							=> phrase('content'),
				'placement'							=> phrase('placement'),
				'start_date'						=> phrase('start_date'),
				'end_date'							=> phrase('end_date'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated'),
				'language_id'						=> phrase('language'),
				'status'							=> phrase('status')
			)
		)
		
		->render($this->_table);
	}
	
	public function validate_end_date($value = null)
	{
		if(strtotime(service('request')->getPost('start_date')) >= strtotime($value))
		{
			return phrase('the_end_date_must_be_greater_than_start_date');
		}
		
		return true;
	}
}
