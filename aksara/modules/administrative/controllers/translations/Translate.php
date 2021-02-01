<?php namespace Aksara\Modules\Administrative\Controllers\Translations;
/**
 * Administrative > Translations > Translate
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Translate extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->set_method('update');
		
		$this->_primary								= service('request')->getGet('id');
		$this->_code								= service('request')->getGet('code');
		$this->_translation_file					= WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->_code . '.json';
		$this->_total_phrase						= 0;
		$this->_limit								= 99;
		$this->_offset								= (service('request')->getGet('per_page') > 1 ? (service('request')->getGet('per_page') * $this->_limit) - $this->_limit : 0);
	}
	
	public function index()
	{
		$this->set_title(phrase('translate'))
		->set_icon('mdi mdi-translate')
		->set_output
		(
			array
			(
				'phrases'							=> $this->_languages(),
				'pagination'						=> array
				(
					'total_rows'					=> $this->_total_phrase,
					'per_page'						=> $this->_limit,
					'offset'						=> $this->_offset
				)
			)
		)
		->form_callback('validate_translation')
		->where
		(
			array
			(
				'id'								=> $this->_primary,
				'code'								=> $this->_code
			)
		)
		
		->render('app__languages');
	}
	
	public function delete_phrase()
	{
		if(DEMO_MODE)
		{
			return throw_exception(403, phrase('changes_will_not_saved_in_demo_mode'), current_page());
		}
		
		$delete_key									= service('request')->getGet('phrase');
		
		/* load the additional helper */
		helper('filesystem');
		
		/* list the file inside the language folder */
		$languages									= get_filenames(WRITEPATH . 'translations');
		$phrases									= array();
		$error										= 0;
		
		if($languages)
		{
			/* merge phrase from whole translation to one variable */
			foreach($languages as $key => $val)
			{
				/* skip if not valid translation */
				if(strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') continue;
				
				/* get translation */
				$phrase								= file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
				
				/* decode phrases */
				$phrase								= json_decode($phrase, true);
				
				/* merge phrases */
				$phrases							= array_merge($phrases, $phrase);
			}
			
			/* prepare to push phrases to translation */
			foreach($languages as $key => $val)
			{
				/* skip if not valid translation */
				if(strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') continue;
				
				/* get translation */
				$phrase								= file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
				
				/* decode phrases */
				$phrase								= json_decode($phrase, true);
				
				/* add new phrase */
				foreach($phrases as $_key => $_val)
				{
					/* push phrase into existing if not exists */
					if(!isset($phrase[$_key]))
					{
						$phrase[$_key]				= ucwords(str_replace('_', ' ', $_key));
					}
				}
				
				/* sort and humanize the order of phrase */
				ksort($phrase);
				
				/* delete phrase */
				unset($phrase[$delete_key]);
				
				/* try to add language file */
				if(!is_writable(WRITEPATH . 'translations') || !file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrase)))
				{
					/* failed to write file, throw an error exception */
					$error++;
				}
			}
		}
		
		if($error)
		{
			return throw_exception(403, phrase('cannot_delete_the_phrase_because_translation_path_is_not_writable'), current_page('../'));
		}
		
		return throw_exception(301, phrase('selected_phrase_was_successfully_removed'), current_page('../'));
	}
	
	public function validate_translation()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
		}
		
		$this->form_validation->setRule('phrase[]', phrase('phrase'), 'trim');
		
		if($this->form_validation->run() === false)
		{
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		if(file_exists($this->_translation_file))
		{
			$phrase									= file_get_contents($this->_translation_file);
			$phrase									= json_decode($phrase, true);
			
			foreach(service('request')->getPost('phrase') as $key => $val)
			{
				if(isset($phrase[$key]))
				{
					$phrase[$key]					= $val;
				}
			}
			
			if(is_writable($this->_translation_file) && file_put_contents($this->_translation_file, json_encode($phrase)))
			{
				return throw_exception(301, phrase('data_was_successfully_submitted'), current_page());
			}
			else
			{
				return throw_exception(403, phrase('unable_to_rewrite_language_file'), current_page());
			}
		}
		else
		{
			return throw_exception(404, phrase('no_language_file_were_found'), current_page());
		}
	}
	
	private function _languages()
	{
		/* check if translation file is exists */
		if(file_exists($this->_translation_file))
		{
			$phrase									= file_get_contents($this->_translation_file);
			$phrase									= json_decode($phrase, true);
			
			$phrases								= array();
			
			if($phrase)
			{
				foreach($phrase as $key => $val)
				{
					if(service('request')->getGet('q') && stripos($val, service('request')->getGet('q')) === false) continue;
					
					$phrases[$key]					= htmlspecialchars($val);
				}
			}
			
			$this->_total_phrase					= sizeof($phrases);
			
			/* slice array */
			$phrases								= array_slice($phrases, $this->_offset, $this->_limit);
			
			return $phrases;
		}
		
		return array();
	}
}
