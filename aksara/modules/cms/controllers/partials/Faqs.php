<?php namespace Aksara\Modules\Cms\Controllers\Partials;
/**
 * CMS > Partials > FAQ
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Faqs extends \Aksara\Laboratory\Core
{
	private $_table									= 'pages__faqs';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('faqs'))
		->set_icon('mdi mdi-file-question')
		->unset_column('faq_id, created_timestamp, updated_timestamp, language')
		->unset_field('faq_id')
		->unset_view('faq_id')
		->set_field
		(
			array
			(
				'faq_description'					=> 'textarea',
				'faq_content'						=> 'faqs',
				'language'							=> 'language_picker',
				'created_timestamp'					=> 'current_timestamp',
				'updated_timestamp'					=> 'current_timestamp',
				'status'							=> 'boolean'
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
				'title'								=> 'required',
				'language_id'						=> 'required',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'faq_title'							=> phrase('title'),
				'faq_description'					=> phrase('description'),
				'faq_content'						=> phrase('contents'),
				'language'							=> phrase('language'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated'),
				'language_id'						=> phrase('language'),
				'status'							=> phrase('status')
			)
		)
		
		->render($this->_table);
	}
}
