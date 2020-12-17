<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Assets > Style
 * Merge multiple css into single file.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Styles extends Aksara
{
	private $_rtl									= false;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('user_agent');
		
		if(get_userdata('language') && in_array(get_userdata('language'), array('arabic')))
		{
			$this->_rtl								= true;
		}
	}
	
	public function index()
	{
		$file_list									= array
		(
			ASSET_PATH . '/bootstrap/bootstrap' . ($this->_rtl ? '.rtl' : null) . '.min.css',
			ASSET_PATH . '/mcustomscrollbar/jquery.mCustomScrollbar.min.css',
			ASSET_PATH . '/select2/select2.min.css',
			ASSET_PATH . '/select2/select2.bootstrap4.min.css',
			ASSET_PATH . '/datepicker/datepicker.min.css',
			ASSET_PATH . '/fileuploader/fileuploader.min.css',
			ASSET_PATH . '/local/css/override.min.css',
			(strtolower($this->agent->browser()) == 'internet explorer' ? ASSET_PATH . '/local/css/ie.fix.min.css' : null) /* only applied to IE */
		);

		/**
		 * Ideally, you wouldn't need to change any code beyond this point.
		 */
		$buffer										= '';
		foreach($file_list as $css => $src)
		{
			$buffer									.= @file_get_contents($src);
		}
		
		if($this->_rtl)
		{
			$buffer									.= @file_get_contents(ASSET_PATH . '/local/css/override.rtl.min.css');
		}
		
		$this->output->set_content_type('css', 'utf-8');
		$this->output->set_output($buffer);
	}
}
