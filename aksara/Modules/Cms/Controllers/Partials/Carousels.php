<?php

namespace Aksara\Modules\Cms\Controllers\Partials;

/**
 * CMS > Partials
 * Manage frontend carousel slideshow
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Carousels extends \Aksara\Laboratory\Core
{
	private $_table									= 'pages__carousels';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_upload_path('carousels');
	}
	
	public function index()
	{
		$this->add_filter($this->_filter());
		
		if(service('request')->getGet('language'))
		{
			$this->where('language_id', service('request')->getGet('language'));
		}
		
		$this->set_title(phrase('carousels'))
		->set_icon('mdi mdi-view-carousel')
		->unset_column('carousel_id, created_timestamp, updated_timestamp, language')
		->unset_field('carousel_id')
		->unset_view('carousel_id')
		->set_field
		(
			array
			(
				'carousel_description'				=> 'textarea',
				'carousel_content'					=> 'carousels',
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
				'carousel_title'					=> 'required',
				'language_id'						=> 'required',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'carousel_title'					=> phrase('title'),
				'carousel_description'				=> phrase('description'),
				'carousel_content'					=> phrase('contents'),
				'language'							=> phrase('language'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated'),
				'language_id'						=> phrase('language'),
				'status'							=> phrase('status')
			)
		)
		
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
