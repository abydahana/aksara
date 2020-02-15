<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Translate Language
 * Translate available phrase to current language.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Translate extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		$this->set_method('update');
		$this->parent_module('translations');
		
		$this->_primary								= $this->input->get('id');
		$this->_code								= $this->input->get('code');
		$this->_translation_file					= 'public/languages/' . $this->_code . '.json';
		$this->_total_phrase						= 0;
		$this->_limit								= 100;
		$this->_offset								= ($this->input->get('per_page') > 1 ? ($this->input->get('per_page') * $this->_limit) - $this->_limit : 0);
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
	
	public function validate_translation()
	{
		if(defined('DEMO_MODE') && DEMO_MODE)
		{
			return throw_exception(301, phrase('changes_will_not_saved_in_demo_mode'), $this->_redirect_back);
		}
		
		/* load additional library and helper */
		$this->load->library('form_validation');
		$this->load->helper('security');
		
		$this->form_validation->set_rules('phrase[]', phrase('phrase'), 'xss_clean');
		
		if($this->form_validation->run() === false)
		{
			return throw_exception(400, $this->form_validation->error_array());
		}
		
		if(file_exists($this->_translation_file))
		{
			$phrase									= file_get_contents($this->_translation_file);
			$phrase									= json_decode($phrase, true);
			
			foreach($this->input->post('phrase') as $key => $val)
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
			$this->_total_phrase					= sizeof($phrase);
			
			/* slice array */
			$phrase									= array_slice($phrase, $this->_offset, $this->_limit);
			return $phrase;
		}
		return array();
	}
}