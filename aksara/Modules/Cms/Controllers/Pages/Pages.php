<?php

namespace Aksara\Modules\Cms\Controllers\Pages;

/**
 * CMS > Pages
 * Manage frontend pages
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Pages extends \Aksara\Laboratory\Core
{
	private $_table									= 'pages';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->add_filter($this->_filter());
		
		if(service('request')->getGet('language'))
		{
			$this->where('language_id', service('request')->getGet('language'));
		}
		
		$this->set_title(phrase('pages'))
		->set_icon('mdi mdi-file-document-outline')
		->set_primary('page_id')
		->unset_column('page_id, author, page_slug, page_content, carousel_title, faq_title, created_timestamp, updated_timestamp, language')
		->unset_field('page_id, author')
		->unset_view('page_id, author')
		->column_order('page_title, page_description, carousel_title, faq_title, updated, status')
		->field_order('page_title, page_description, carousel_id, faq_id, language_id, created_timestamp, updated_timestamp, status')
		->set_field
		(
			array
			(
				'page_description'					=> 'textarea',
				'page_content'						=> 'wysiwyg',
				'created_timestamp'					=> 'current_timestamp',
				'updated_timestamp'					=> 'current_timestamp',
				'status'							=> 'boolean'
			)
		)
		->set_field('page_slug', 'to_slug', 'page_title')
		->set_field('page_title', 'hyperlink', 'pages', array('page_slug' => 'page_slug'), true)
		
		->add_action('option', '../../pages', phrase('view_page'), 'btn-success', 'mdi mdi-eye', array('page_id' => 'page_id'), true)
		
		->set_relation
		(
			'carousel_id',
			'pages__carousels.carousel_id',
			'{pages__carousels.carousel_title}',
			array
			(
				'pages__carousels.status'			=> 1
			)
		)
		->set_relation
		(
			'faq_id',
			'pages__faqs.faq_id',
			'{pages__faqs.faq_title}',
			array
			(
				'pages__faqs.status'				=> 1
			)
		)
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
				'page_title'						=> 'required|max_length[64]|unique[' . $this->_table . '.page_title.page_id.' . service('request')->getGet('page_id') . ']',
				'page_content'						=> 'required',
				'status'							=> 'boolean'
			)
		)
		->set_default
		(
			array
			(
				'author'							=> get_userdata('user_id')
			)
		)
		->set_alias
		(
			array
			(
				'page_title'						=> phrase('title'),
				'page_description'					=> phrase('description'),
				'page_slug'							=> phrase('slug'),
				'page_content'						=> phrase('content'),
				'carousel_id'						=> phrase('carousel'),
				'carousel_title'					=> phrase('carousel'),
				'faq_id'							=> phrase('faq'),
				'faq_title'							=> phrase('faq'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated'),
				'language'							=> phrase('language'),
				'language_id'						=> phrase('language')
			)
		)
		->field_position
		(
			array
			(
				'carousel_id'						=> 2,
				'faq_id'							=> 2,
				'created_timestamp'					=> 2,
				'updated_timestamp'					=> 2,
				'status'							=> 2,
				'language_id'						=> 2
			)
		)
		->column_size
		(
			array
			(
				1									=> 'col-md-8',
				2									=> 'col-md-4'
			)
		)
		->modal_size('modal-xl')
		
		->render($this->_table);
	}
	
	private function _filter()
	{
		$languages									= '<option value="0">' . phrase('all_languages') . '</option>';
		
		$languages_query							= $this->model->get_where
		(
			'app__languages',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		if($languages_query)
		{
			foreach($languages_query as $key => $val)
			{
				$languages							.= '<option value="' . $val->id . '"' . ($val->id == service('request')->getGet('language') ? ' selected' : null) . '>' . $val->language . '</option>';
			}
		}
		
		return '
			<select name="language" class="form-control form-control-sm bordered" placeholder="' . phrase('language') . '">
				' . $languages . '
			</select>
		';
	}
}
