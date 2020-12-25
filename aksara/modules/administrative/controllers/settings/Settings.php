<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Administrative > Settings
 *
 * @version			2.1.1
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
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->crud();
		
		$this->set_method('update');
		$this->set_upload_path('settings');
	}
	
	public function index()
	{
		$required_api_key							= null;
		$required_facebook_app_id					= null;
		$required_facebook_app_secret				= null;
		$required_google_client_id					= null;
		$required_google_client_secret				= null;
		$required_email_masking						= null;
		$required_smtp_host							= null;
		
		if($this->input->post('openlayers_search_provider'))
		{
			$required_api_key						= 'required|';
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
		
		if($this->input->post('smtp_email_masking'))
		{
			$required_email_masking					= 'valid_email';
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
				'username_changes'					=> 'boolean',
				'frontend_registration'				=> 'boolean',
				'auto_active_registration'			=> 'boolean',
				'facebook_app_secret'				=> 'encryption',
				'google_client_secret'				=> 'encryption',
				'smtp_password'						=> 'encryption'
			)
		)
		->set_field
		(
			'openlayers_search_provider',
			'radio',
			array
			(
				''									=> 'OpenLayers',
				'google'							=> 'Google',
				'osm'								=> 'OpenStreetMap'
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
				'group_id > '						=> 2
			)
		)
		->set_validation
		(
			array
			(
				'app_name'							=> 'required|xss_clean|max_length[60]',
				'app_description'					=> 'xss_clean',
				'office_email'						=> 'required|valid_email',
				
				/* MEMBERSHIP */
				'username_changes'					=> 'is_boolean',
				'frontend_registration'				=> 'is_boolean',
				'auto_active_registration'			=> 'is_boolean',
				'one_device_login'					=> 'is_boolean',
				
				/* APIS */
				'openlayers_search_provider'		=> 'in_list[google,osm]',
				'openlayers_search_key'				=> $required_api_key . 'alpha_dash|max_length[128]',
				'maps_provider'						=> 'in_list[disabled,google,openlayers]',
				'google_analytics_key'				=> 'alpha_dash|max_length[32]',
				
				/* OAUTH */
				'facebook_app_id'					=> $required_facebook_app_id,
				'facebook_app_secret'				=> $required_facebook_app_secret,
				'google_client_id'					=> $required_google_client_id,
				'google_client_secret'				=> $required_google_client_secret,
				
				/* NOTIFIER */
				'smtp_email_masking'				=> $required_email_masking,
				'smtp_port'							=> 'numeric'
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
				'one_device_login'					=> phrase('one_device_login'),
				'default_membership_group'			=> phrase('default_membership_group'),
				'auto_active_registration'			=> phrase('auto_active_registration'),
				'username_changes'					=> phrase('enable_username_changes'),
				'frontend_registration'				=> phrase('enable_front_end_registration'),
				
				/* APIS */
				'maps_provider'						=> phrase('maps_provider'),
				'openlayers_search_provider'		=> phrase('openlayers_search_provider'),
				'openlayers_search_key'				=> phrase('openlayers_search_key'),
				'google_analytics_key'				=> phrase('google_analytics_key'),
				
				/* OATH */
				'facebook_app_id'					=> phrase('facebook_app_id'),
				'facebook_app_secret'				=> phrase('facebook_app_secret'),
				'google_client_id'					=> phrase('google_client_id'),
				'google_client_secret'				=> phrase('google_client_secret'),
				
				/* NOTIFIER */
				'smtp_email_masking'				=> phrase('smtp_email_masking'),
				'smtp_sender_masking'				=> phrase('smtp_sender_masking'),
				'smtp_host'							=> phrase('smtp_host'),
				'smtp_port'							=> phrase('smtp_port'),
				'smtp_username'						=> phrase('smtp_username'),
				'smtp_password'						=> phrase('smtp_password')
			)
		)
		->where
		(
			array
			(
				'id'								=> 1
			)
		)
		->limit(1)
		->render($this->_table);
	}
}
