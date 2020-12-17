<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Assets > Scripts
 * Merge multiple javascript into single file.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Scripts extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('user_agent');
	}
	
	public function index()
	{
		$language_id								= ($this->session->userdata('language_id') ? $this->session->userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
		
		$language									= $this->db->select
		('
			locale
		')
		->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $language_id
			)
		)
		->row('locale');
		
		$language									= strstr($language, ',', true);
		
		$file_list									= array
		(
			ASSET_PATH . '/jquery/jquery.min.js',
			ASSET_PATH . '/local/js/require.min.js',
			ASSET_PATH . '/popper/popper.min.js',
			ASSET_PATH . '/bootstrap/bootstrap.min.js',
			ASSET_PATH . '/actual/actual.min.js',
			ASSET_PATH . '/mcustomscrollbar/jquery.mousewheel.min.js',
			ASSET_PATH . '/mcustomscrollbar/jquery.mCustomScrollbar.min.js',
			ASSET_PATH . '/select2/select2.min.js',
			ASSET_PATH . '/datepicker/datepicker.min.js',
			ASSET_PATH . '/fileuploader/fileuploader.min.js',
			(strtolower($this->agent->browser()) == 'internet explorer' ? ASSET_PATH . '/local/js/ie.fix.min.js' : null), /* only applied to IE */
			ASSET_PATH . '/visible/visible.min.js',
			ASSET_PATH . '/scanner/scanner.min.js',
			ASSET_PATH . '/lazyload/lazyload.min.js',
			ASSET_PATH . '/jszip/jszip-utils.min.js',
			ASSET_PATH . '/jszip/jszip.min.js',
			ASSET_PATH . '/local/js/function.min.js',
			ASSET_PATH . '/local/js/global.min.js',
			ASSET_PATH . '/local/js/component.min.js'
		);

		/**
		 * Ideally, you wouldn't need to change any code beyond this point.
		 */
		$buffer										= '
			var config =
			{
				base_url: "' . htmlspecialchars(base_url()) . '",
				asset_url: "' . htmlspecialchars(asset_url()) . '",
				app_name: "' . htmlspecialchars(get_setting('app_name')) . '",
				app_icon: "' . htmlspecialchars(get_image('settings', get_setting('app_icon'), 'icon')) . '",
				content_wrapper: "#content-wrapper",
				registration_enabled: ' . (int) get_setting('frontend_registration') . ',
				language: "' . htmlspecialchars($language) . '",
				openlayers_search_provider: "' . htmlspecialchars(get_setting('openlayers_search_provider')) . '",
				openlayers_search_key: "' . htmlspecialchars(get_setting('openlayers_search_key')) . '",
				map_center: ' . (json_decode(get_setting('office_map')) ? get_setting('office_map') : '{}') . ',
				google_auth: ' . (get_setting('google_client_id') && get_setting('google_client_secret') ? 'true' : 'false') . ',
				facebook_auth: ' . (get_setting('facebook_app_id') && get_setting('facebook_app_secret') ? 'true' : 'false') . '
				
			},
			phrase									= ' . json_encode(json_decode($this->_i18n()), JSON_UNESCAPED_SLASHES) . ';
		';
		
		foreach($file_list as $js => $src)
		{
			if(file_exists($src))
			{
				$buffer								.= file_get_contents($src);
			}
		}
		
		$this->output->set_content_type('js', 'utf-8');
		$this->output->set_output($buffer);
	}
	
	private function _i18n()
	{
		$language_id								= (get_userdata('language_id') ? get_userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
		$language									= $this->db->select
		('
			code
		')
		->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $language_id
			),
			1
		)
		->row('code');
		
		if(file_exists(TRANSLATION_PATH . DIRECTORY_SEPARATOR . $language . '.json'))
		{
			return file_get_contents(TRANSLATION_PATH . DIRECTORY_SEPARATOR . $language . '.json');
		}
		
		return '[]';
	}
}
