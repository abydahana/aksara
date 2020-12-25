<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Partials > Announcements
 * Manage announcements.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Announcements extends Aksara
{
	private $_table									= 'app__announcements';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
		
		$this->crud();
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_announcements'))
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
				'title'								=> 'required|max_length[256]|is_unique[' . $this->_table . '.title.announcement_id.' . $this->input->get('announcement_id') . ']',
				'content'							=> 'required',
				'language_id'						=> 'required',
				'status'							=> 'is_boolean'
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
}
