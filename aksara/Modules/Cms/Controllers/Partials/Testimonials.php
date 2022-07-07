<?php

namespace Aksara\Modules\Cms\Controllers\Partials;

/**
 * CMS > Partials > Testimonials
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Testimonials extends \Aksara\Laboratory\Core
{
	private $_table									= 'testimonials';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_upload_path('testimonials');
	}
	
	public function index()
	{
		$this->add_filter($this->_filter());
		
		if(service('request')->getGet('language'))
		{
			$this->where('language_id', service('request')->getGet('language'));
		}
		
		$this->set_title(phrase('testimonials'))
		->set_icon('mdi mdi-comment-account-outline')
		->set_primary('testimonial_id')
		->unset_column('testimonial_id, testimonial_content, timestamp, language')
		->unset_field('testimonial_id')
		->unset_view('testimonial_id')
		->set_field
		(
			array
			(
				'photo'								=> 'image',
				'testimonial_content'				=> 'textarea',
				'timestamp'							=> 'current_timestamp',
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
				'first_name'						=> 'required|string',
				'last_name'							=> 'string',
				'testimonial_title'					=> 'required|string',
				'testimonial_content'				=> 'required|string',
				'language_id'						=> 'required',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'first_name'						=> phrase('first_name'),
				'last_name'							=> phrase('last_name'),
				'testimonial_title'					=> phrase('title'),
				'testimonial_content'				=> phrase('testimony'),
				'language_id'						=> phrase('language'),
				'status'							=> phrase('status')
			)
		)
		->merge_field('first_name, last_name')
		->merge_content('{first_name} {last_name}', phrase('full_name'))
		
		->order_by('timestamp', 'DESC')
		
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
