<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pages
 * Render the frontend page. Also to be used as default routes.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Pages extends Aksara
{
	private $_table									= 'pages';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($slug = null)
	{
		if(!$slug && $this->input->get('page_slug'))
		{
			$slug									= $this->input->get('page_slug');
		}
		
		$this->set_title('{page_title}', phrase('page_not_found'))
		->set_description('{page_description}')
		->set_icon('mdi mdi-file-document-outline')
		->set_output
		(
			'suggestions',
			$this->model
			->select
			('
				page_slug,
				page_title
			')
			->get_where($this->_table, array('status' => 1), 8)
			->result_array()
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
		->where
		(
			array
			(
				'page_slug'							=> ($slug ? $slug : ''),
				'status'							=> 1
			)
		)
		->limit(1)
		->render($this->_table);
	}
}
