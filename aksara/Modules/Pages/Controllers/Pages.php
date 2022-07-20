<?php

namespace Aksara\Modules\Pages\Controllers;

/**
 * Pages
 * Render the frontend page. Also to be used as default routes.
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
		
		$this->searchable(false);
	}
	
	public function index($slug = null)
	{
		if($slug)
		{
			$this->where
			(
				array
				(
					'pages.page_slug'				=> $slug,
					'pages.status'					=> 1
				)
			);
		}
		else if(service('request')->getGet('page_id'))
		{
			$this->where('page_id', service('request')->getGet('page_id'));
		}
		
		$this->set_title('{page_title}', phrase('page_not_found'))
		->set_description('{page_description}')
		->set_icon('mdi mdi-file-document-outline')
		->set_output
		(
			array
			(
				'suggestions'						=> $this->model->select
				('
					page_slug,
					page_title
				')
				->get_where
				(
					$this->_table,
					array
					(
						'status'					=> 1,
						'language_id'				=> get_userdata('language_id')
					),
					8
				)
				->result_array()
			)
		)
		->set_relation
		(
			'faq_id',
			'pages__faqs.faq_id',
			'{pages__faqs.faq_content}',
			array
			(
				'pages__faqs.status'				=> 1
			)
		)
		->set_relation
		(
			'carousel_id',
			'pages__carousels.carousel_id',
			'{pages__carousels.carousel_content}',
			array
			(
				'pages__carousels.status'			=> 1
			)
		)
		->order_by('(CASE WHEN pages.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		->limit(1)
		
		->render($this->_table);
	}
	
	public function not_found()
	{
		$this->set_title(phrase('page_not_found'))
		->set_description(phrase('the_page_you_requested_does_not_exist'))
		->set_output
		(
			array
			(
				'suggestions'						=> $this->model->select
				('
					page_slug,
					page_title
				')
				->get_where
				(
					$this->_table,
					array
					(
						'status'					=> 1
					),
					8
				)
				->result()
			)
		)
		->render();
	}
}
