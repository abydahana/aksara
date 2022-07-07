<?php

namespace Aksara\Modules\Assets\Controllers;

/**
 * Assets controller
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Assets extends \Aksara\Laboratory\Core
{
	private $_rtl									= false;
	
	public function __construct()
	{
		parent::__construct();
		
		if(get_userdata('language') && in_array(get_userdata('language'), array('ar')))
		{
			$this->_rtl								= true;
		}
	}
	
	public function index()
	{
		return throw_exception(404, phrase('the_page_you_requested_does_not_exist'), base_url());
	}
	
	public function themes()
	{
		$extension									= strtolower(pathinfo(service('request')->uri->getPath(), PATHINFO_EXTENSION));
		
		if(in_array($extension, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'bmp']) && file_exists('../' . service('request')->uri->getPath()))
		{
			helper('download');
			
			return force_download(service('request')->uri->getPath(), file_get_contents('../' . service('request')->uri->getPath()), true);
		}
	}
	
	public function styles()
	{
		$user_agent									= service('request')->getUserAgent();
		
		$file_list									= array
		(
			'assets/mcustomscrollbar/jquery.mCustomScrollbar.min.css',
			'assets/select2/select2.min.css',
			'assets/select2/select2.bootstrap4.min.css',
			'assets/datepicker/datepicker.min.css',
			'assets/fileuploader/fileuploader.min.css',
			'assets/local/css/override.min.css',
			(strtolower($user_agent->getBrowser()) == 'internet explorer' ? 'assets/local/css/ie.fix.min.css' : null) /* only applied to IE */
		);
		
		/**
		 * Ideally, you wouldn't need to change any code beyond this point.
		 */
		$output										= '';
		
		foreach($file_list as $key => $source)
		{
			if(!$source) continue;

			$output									.= @file_get_contents($source);
		}
		
		if($this->_rtl)
		{
			$output									.= @file_get_contents('local/css/override.rtl.min.css');
		}
		
		service('response')->setHeader('Content-Type', 'text/css');
		service('response')->setBody($output);
		
		return service('response')->send();
	}
	
	public function scripts()
	{
		$user_agent									= service('request')->getUserAgent();
		
		$file_list									= array
		(
			'assets/jquery/jquery.min.js',
			'assets/local/js/require.min.js',
			'assets/sprintf/sprintf.min.js',
			'assets/actual/actual.min.js',
			'assets/mcustomscrollbar/jquery.mousewheel.min.js',
			'assets/mcustomscrollbar/jquery.mCustomScrollbar.min.js',
			'assets/select2/select2.min.js',
			'assets/datepicker/datepicker.min.js',
			(strtolower($user_agent->getBrowser()) == 'internet explorer' ? 'assets/local/js/ie.fix.min.js' : null), /* only applied to IE */
			'assets/visible/visible.min.js',
			'assets/jszip/jszip-utils.min.js',
			'assets/jszip/jszip.min.js',
			'assets/local/js/function.min.js',
			'assets/local/js/global.min.js',
			'assets/local/js/component.min.js'
		);
		
		/**
		 * Ideally, you wouldn't need to change any code beyond this point.
		 */
		$output										= '
			var config =
			{
				base_url: "' . htmlspecialchars(preg_replace('/\?.*/', '', base_url())) . '",
				asset_url: "' . htmlspecialchars(preg_replace('/\?.*/', '', base_url('assets'))) . '/",
				current_slug: "' . htmlspecialchars(str_replace('.', '-', service('uri')->setSilent()->getPath())) . '",
				is_logged: ' . (get_userdata('is_logged') ? 1 : '0') . ',
				document_extension_allowed: ' . (json_encode(explode(',', DOCUMENT_FORMAT_ALLOWED)) ? json_encode(explode(',', DOCUMENT_FORMAT_ALLOWED)) : '[]') . ',
				image_extension_allowed: ' . (json_encode(explode(',', IMAGE_FORMAT_ALLOWED)) ? json_encode(explode(',', IMAGE_FORMAT_ALLOWED)) : '[]') . ',
				max_upload_size: ' . (MAX_UPLOAD_SIZE ? MAX_UPLOAD_SIZE : '0') . ',
				app_name: "' . htmlspecialchars(get_setting('app_name')) . '",
				app_logo: "' . htmlspecialchars(get_image('settings', get_setting('app_logo'))) . '",
				app_icon: "' . htmlspecialchars(get_image('settings', get_setting('app_icon'), 'icon')) . '",
				content_placeholder: "#content-placeholder",
				registration_enabled: ' . (int) get_setting('frontend_registration') . ',
				language: "' . htmlspecialchars(get_userdata('language')) . '",
				timezone: "' . TIMEZONE . '",
				openlayers_search_provider: "' . htmlspecialchars(get_setting('openlayers_search_provider')) . '",
				openlayers_search_key: "' . htmlspecialchars(get_setting('openlayers_search_key')) . '",
				default_map_tile: "' . get_setting('default_map_tile') . '",
				map_center: ' . (json_decode(get_setting('office_map')) ? get_setting('office_map') : '{}') . ',
				google_auth: ' . (get_setting('google_client_id') && get_setting('google_client_secret') ? 'true' : 'false') . ',
				facebook_auth: ' . (get_setting('facebook_app_id') && get_setting('facebook_app_secret') ? 'true' : 'false') . ',
				active_years: ' . json_encode($this->_get_active_years()) . ',
				action_sound: ' . (get_setting('action_sound') ? 'true' : 'false') . '
				
			},
			phrase									= ' . json_encode(json_decode($this->_i18n()), JSON_UNESCAPED_SLASHES) . ';
		';
		
		foreach($file_list as $key => $source)
		{
			if(!$source) continue;
			
			$output									.= @file_get_contents($source);
		}
		
		service('response')->setHeader('Content-Type', 'text/javascript');
		service('response')->setBody($output);
		
		return service('response')->send();
	}
	
	private function _i18n()
	{
		if(file_exists(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . get_userdata('language') . '.json'))
		{
			return file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . get_userdata('language') . '.json');
		}
		
		return '{}';
	}
	
	private function _get_active_years()
	{
		$output										= array();
		
		$query										= $this->model->get_where
		(
			'app__years',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$output[]							= array
				(
					'value'							=> $val->year,
					'label'							=> $val->year,
					'selected'						=> $val->default
				);
			}
		}
		
		return $output;
	}
}
