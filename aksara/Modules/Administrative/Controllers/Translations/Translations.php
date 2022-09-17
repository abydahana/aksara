<?php

namespace Aksara\Modules\Administrative\Controllers\Translations;

/**
 * Administrative > Translations
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Translations extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__languages';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_update('id', array(1));
		$this->unset_delete('id', array(1));
	}
	
	public function index()
	{
		$this->set_title(phrase('translations'))
		->set_icon('mdi mdi-translate')
		->set_description
		('
			<div class="row">
				<div class="col-12">
					' . phrase('click_the_synchronize_button_to_equate_the_phrases_for_each_translations') . '
				</div>
			</div>
		')
		->unset_column('id')
		->unset_field('id')
		->unset_view('id')
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->add_action('toolbar', 'synchronize', phrase('synchronize'), 'btn btn-info --xhr show-progress', 'mdi mdi-reload')
		->add_action('option', 'translate', phrase('translate'), 'btn btn-success --xhr', 'mdi mdi-comment-processing-outline', array('id' => 'id', 'code' => 'code', 'keyword' => null))
		->set_validation
		(
			array
			(
				'language'							=> 'required|string|max_length[32]',
				'description'						=> 'required|string',
				'code'								=> 'required|alpha_dash|max_length[32]|unique[app__languages.code.id.' . service('request')->getGet('id') . ']',
				'locale'							=> 'required|string|max_length[64]',
				'status'							=> 'boolean'
			)
		)
		->set_alias
		(
			array
			(
				'language'							=> phrase('language'),
				'description'						=> phrase('description'),
				'code'								=> phrase('code'),
				'locale'							=> phrase('locale'),
				'status'							=> phrase('status')
			)
		)
		
		->render($this->_table);
	}
	
	public function after_insert()
	{
		/* try to add language file */
		try
		{
			/* check if language directory is exists */
			if(!is_dir(WRITEPATH . 'translations') && mkdir(WRITEPATH . 'translations', 0755, true))
			{
				/* put content into file */
				file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . service('request')->getPost('code') . '.json', json_encode(array()));
			}
			else
			{
				/* put content into file */
				file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . service('request')->getPost('code') . '.json', json_encode(array()));
			}
		}
		catch(Exception $e)
		{
			/* failed to write file */
		}
	}
	
	public function after_update()
	{
		/* try to update language file */
		try
		{
			/* check if language directory is exists */
			if(file_exists(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . service('request')->getGet('code') . '.json'))
			{
				/* rename old file */
				rename(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . service('request')->getGet('code') . '.json', WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . service('request')->getPost('code') . '.json');
			}
		}
		catch(Exception $e)
		{
			/* failed to write file */
		}
	}
}
