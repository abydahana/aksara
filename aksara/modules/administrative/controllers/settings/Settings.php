<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Settings
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Settings extends Aksara
{
	private $_table									= 'app__settings';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1); // only user with group id 1 can access this module
		$this->set_theme('backend');
		$this->set_method('update');
		$this->set_upload_path('settings');
	}
	
	public function index()
	{
		$required_gmaps_api							= null;
		$required_omaps_api							= null;
		$required_facebook_app_id					= null;
		$required_facebook_app_secret				= null;
		$required_google_client_id					= null;
		$required_google_client_secret				= null;
		
		if('google' == $this->input->post('maps_provider') || 'google' == $this->input->post('openlayers_search_provider'))
		{
			$required_gmaps_api						= 'required|';
		}
		elseif('openlayers' == $this->input->post('maps_provider'))
		{
			$required_omaps_api						= 'required|';
		}
		
		if($this->input->post('facebook_app_id'))
		{
			$required_facebook_app_secret			= 'required';
		}
		elseif($this->input->post('facebook_app_secret'))
		{
			$required_facebook_app_id				= 'required';
		}
		
		if($this->input->post('google_client_id'))
		{
			$required_google_client_secret			= 'required';
		}
		elseif($this->input->post('google_client_secret'))
		{
			$required_google_client_id				= 'required';
		}
		
		$this->set_title(phrase('application_settings'))
		->set_icon('mdi mdi-wrench-outline')
		->set_primary('id')
		->unset_field('id')
		->set_field
		(
			array
			(
				'app_description'					=> 'textarea',
				'app_logo'							=> 'image',
				'app_icon'							=> 'image',
				'reports_logo'						=> 'image',
				'office_address'					=> 'textarea',
				'office_map'						=> 'coordinate',
				'one_device_login'					=> 'boolean',
				'login_annually'					=> 'boolean',
				'enable_username_changes'			=> 'boolean',
				'enable_frontend_registration'		=> 'boolean',
				'auto_active_registration'			=> 'boolean',
				'google_maps_api_key'				=> 'textarea'
			)
		)
		->set_field
		(
			'openlayers_search_provider',
			'dropdown',
			array
			(
				'google'							=> 'Google',
				'osm'								=> 'OpenStreetMap',
				'bing'								=> 'Bing!',
				'mapquest'							=> 'MapQuest',
				'photon'							=> 'Photon',
				'pelias'							=> 'Pelias',
				'opencage'							=> 'OpenCage'
			)
		)
		->set_field
		(
			'maps_provider',
			'dropdown',
			array
			(
				'disabled'							=> phrase('disabled'),
				'google'							=> 'Google',
				'openlayers'						=> 'OpenLayers'
			)
		)
		->set_field
		(
			'versi_simda',
			'radio',
			array
			(
				0									=> 'Versi 2.7.0.10 kebawah atau sama (Menggunakan Jurnal)',
				1									=> 'Versi 2.7.0.12 (Menggunakan SP3B dan SP2B)'
			)
		)
		->set_relation
		(
			'app_language',
			'app__languages.id',
			'{app__languages.language}',
			array
			(
				'app__languages.status'				=> 1
			)
		)
		->set_relation
		(
			'default_membership_group',
			'app__groups.group_id',
			'{app__groups.group_name}',
			array
			(
				'group_id > '						=> 1
			)
		)
		->set_validation
		(
			array
			(
				'app_name'							=> 'required|xss_clean|max_length[60]',
				'app_description'					=> 'xss_clean',
				'office_email'						=> 'required|valid_email',
				'login_annually'					=> 'is_boolean',
				
				/* MEMBERSHIP */
				'enable_username_changes'			=> 'is_boolean',
				'enable_frontend_registration'		=> 'is_boolean',
				'auto_active_registration'			=> 'is_boolean',
				'one_device_login'					=> 'is_boolean',
				
				/* APIS */
				'google_maps_api_key'				=> $required_gmaps_api . 'xss_clean',
				'openlayers_search_provider'		=> 'in_list[google,osm,bing,mapquest,photon,pelias,opencage]',
				'openlayers_search_key'				=> $required_omaps_api . 'alpha_dash|max_length[128]',
				'maps_provider'						=> 'in_list[disabled,google,openlayers]',
				'google_analytics_key'				=> 'alpha_dash|max_length[32]',
				'facebook_fanpage'					=> 'valid_url',
				'facebook_app_id'					=> $required_facebook_app_id,
				'facebook_app_secret'				=> $required_facebook_app_secret,
				'google_client_id'					=> $required_google_client_id,
				'google_client_secret'				=> $required_google_client_secret,
				
				/* SIMDA */
				'nama_pemda'						=> 'xss_clean|max_length[60]',
				'nama_daerah'						=> 'xss_clean|max_length[60]',
				'versi_simda'						=> 'in_list[0,1]'
			)
		)
		->set_alias
		(
			array
			(
				'app_name'							=> phrase('application_name'),
				'app_description'					=> phrase('application_description'),
				'office_email'						=> phrase('office_email'),
				'office_phone'						=> phrase('office_phone'),
				'office_fax'						=> phrase('office_fax'),
				'whatsapp_number'					=> phrase('whatsapp_number'),
				'instagram_username'				=> phrase('instagram_username'),
				'twitter_username'					=> phrase('twitter_username'),
				'office_address'					=> phrase('office_address'),
				'office_map'						=> phrase('office_map'),
				'app_logo'							=> phrase('application_logo'),
				'app_icon'							=> phrase('application_icon'),
				'app_language'						=> phrase('system_language'),
				
				/* MEMBERSHIP */
				'login_annually'					=> phrase('login_annually'),
				'one_device_login'					=> phrase('one_device_login'),
				'enable_frontend_registration'		=> phrase('enable_frontend_registration'),
				'default_membership_group'			=> phrase('default_membership_group'),
				'auto_active_registration'			=> phrase('auto_active_registration'),
				'enable_username_changes'			=> phrase('enable_username_changes'),
				
				/* APIS */
				'maps_provider'						=> phrase('maps_provider'),
				'google_maps_api_key'				=> phrase('google_maps_api_key') . ' (' . phrase('separate_with_comma') . ')',
				'openlayers_search_provider'		=> phrase('openlayers_search_provider'),
				'openlayers_search_key'				=> phrase('openlayers_search_key'),
				'google_analytics_key'				=> phrase('google_analytics_key'),
				'facebook_fanpage'					=> phrase('facebook_fanpage'),
				'facebook_app_id'					=> phrase('facebook_app_id'),
				'facebook_app_secret'				=> phrase('facebook_app_secret'),
				'google_client_id'					=> phrase('google_client_id'),
				'google_client_secret'				=> phrase('google_client_secret')
			)
		)
		->where('id', 1)
		->limit(1)
		->render($this->_table);
	}
}