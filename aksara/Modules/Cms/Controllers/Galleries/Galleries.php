<?php

namespace Aksara\Modules\Cms\Controllers\Galleries;

/**
 * CMS > Galleries
 * Manage galleries
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Galleries extends \Aksara\Laboratory\Core
{
	private $_table									= 'galleries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->grid_view('gallery_images', 'galleries', array('gallery_slug' => 'gallery_slug'), true);
	}
	
	public function index()
	{
		$this->set_title(phrase('galleries'))
		->set_icon('mdi mdi-folder-multiple-image')
		->set_primary('gallery_id')
		->unset_column('gallery_id, gallery_slug, gallery_tags, gallery_attributes, created_timestamp, updated_timestamp, featured')
		->unset_field('gallery_id, author')
		->unset_view('gallery_id, first_name')
		->column_order('gallery_images, gallery_title, gallery_description, first_name, featured, status')
		->field_order('gallery_images, gallery_title, gallery_slug, gallery_description, gallery_attributes, gallery_tags, featured, created_timestamp, updated_timestamp, status')
		->view_order('gallery_images, gallery_title, gallery_slug, gallery_description, gallery_attributes, gallery_tags, featured, created_timestamp, updated_timestamp, status')
		->set_field
		(
			array
			(
				'gallery_images'					=> 'images',
				'gallery_description'				=> 'wysiwyg',
				'gallery_tags'						=> 'tagsinput',
				'gallery_attributes'				=> 'attributes',
				'created_timestamp'					=> 'current_timestamp',
				'updated_timestamp'					=> 'current_timestamp',
				'featured'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_field('gallery_slug', 'to_slug', 'gallery_title')
		->set_field('gallery_title', 'hyperlink', 'galleries', array('gallery_slug' => 'gallery_slug'), true)
		
		->add_action('option', '../../galleries', phrase('view_album'), 'btn-success', 'mdi mdi-eye', array('gallery_slug' => 'gallery_slug'), true)
		
		->add_class('gallery_description', 'minimal')
		->set_relation
		(
			'author',
			'app__users.user_id',
			'{app__users.first_name} {app__users.last_name}'
		)
		->merge_content('{first_name} {last_name}', 'Author')
		->set_validation
		(
			array
			(
				'gallery_title'						=> 'required|max_length[64]|unique[' . $this->_table . '.gallery_title.gallery_id.' . service('request')->getGet('gallery_id') . ']',
				'gallery_description'				=> 'required',
				'featured'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_default('author', get_userdata('user_id'))
		->field_position
		(
			array
			(
				'gallery_tags'						=> 2,
				'featured'							=> 2,
				'created_timestamp'					=> 2,
				'updated_timestamp'					=> 2,
				'status'							=> 2
			)
		)
		->column_size
		(
			array
			(
				1									=> 'col-md-7',
				2									=> 'col-md-5'
			)
		)
		->set_alias
		(
			array
			(
				'gallery_images'					=> phrase('images'),
				'gallery_title'						=> phrase('title'),
				'gallery_slug'						=> phrase('slug'),
				'gallery_description'				=> phrase('description'),
				'gallery_attributes'				=> phrase('attributes'),
				'gallery_tags'						=> phrase('tags'),
				'featured'							=> phrase('featured'),
				'status'							=> phrase('status'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated')
			)
		)
		
		->render($this->_table);
	}
}
