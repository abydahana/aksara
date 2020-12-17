<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Auth
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Auth extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		if(get_setting('google_client_id') && get_setting('google_client_secret'))
		{
			$this->load->library('google');
		}
		
		if(get_setting('facebook_app_id') && get_setting('facebook_app_secret'))
		{
			$this->load->library('fb');
		}
	}
	
	public function index()
	{
		/* check if use is already signed in */
		if(get_userdata('is_logged'))
		{
			return throw_exception(301, phrase('you_have_been_signed_in'), base_url('dashboard'), true);
		}
		
		$this->set_title(phrase('dashboard_access'))
		->set_icon('mdi mdi-lock-open-outline')
		->set_description(phrase('use_your_account_information_to_start_session'))
		
		->form_callback('_validate_form')
		
		->render();
	}
	
	/**
	 * validate form
	 */
	public function _validate_form()
	{
		/* check if system apply one device login */
		if(get_setting('one_device_login'))
		{
			// under research
		}
		
		/* load additional library and helper */
		$this->load->library('form_validation');
		$this->load->helper('security');
		
		$this->form_validation->set_rules('username', phrase('username'), 'required');
		$this->form_validation->set_rules('password', phrase('password'), 'required');
		
		/* run form validation */
		if($this->form_validation->run() === false)
		{
			return throw_exception(400, $this->form_validation->error_array());
		}
		else
		{
			$username								= $this->input->post('username');
			$password								= $this->input->post('password');
			$execute								= $this->model->select
			('
				user_id,
				username,
				password,
				group_id,
				language_id,
				status
			')
			->where('username', $username)
			->or_where('email', $username)
			->get
			(
				'app__users',
				1
			)
			->row();
			
			/* check if user is inactive */
			if($execute && $execute->status != 1)
			{
				return throw_exception(404, phrase('your_account_is_temporary_disabled_or_not_yet_activated'));
			}
			elseif($execute && password_verify($password . SALT, $execute->password))
			{
				/* update the last login timestamp */
				$this->model->update
				(
					'app__users',
					array
					(
						'last_login'				=> date('Y-m-d H:i:s')
					),
					array
					(
						'user_id'					=> $execute->user_id
					),
					1
				);
				
				/* check session store */
				if(1 == $this->input->post('remember_session'))
				{
					/* store session to the current device */
					$this->session->sess_expiration	= 0;
				}
				else
				{
					/* use default session time instead */
					$this->session->sess_expiration	= 900;
				}
				
				/* set the user credential into session */
				$this->session->set_userdata
				(
					array
					(
						'user_id'					=> $execute->user_id,
						'username'					=> $execute->username,
						'group_id'					=> $execute->group_id,
						'language_id'				=> $execute->language_id,
						'is_logged'					=> true,
						'session_generated'			=> time()
					)
				);
				
				return throw_exception(301, phrase('welcome_back') . ', <b>' . get_userdata('first_name') . '</b>! ' . phrase('you_were_logged_in'), base_url('dashboard'), true);
			}
			
			return throw_exception(400, array('password' => phrase('username_or_email_and_password_did_not_match') . '<hr class="mt-0 mb-1" /><a href="' . current_page('forgot') . '" class="--xhr"><b>' . phrase('click_here') . '</b></a> ' . phrase('to_reset_your_password')));
		}
	}
	
	/**
	 * sign out
	 */
	public function sign_out()
	{
		/**
		 * prepare to revoke google sign token
		 */
		if(get_setting('google_client_id') && get_setting('google_client_secret') && get_userdata('oauth_uid'))
		{
			$this->google->revokeToken();
		}
		
		$group_id									= get_userdata('group_id');
		
		$this->session->sess_destroy();
		
		return throw_exception(301, phrase('you_were_logged_out'), base_url(), true);
	}
	/**
	 * redirect to google auth url
	 */
	public function google()
	{
		redirect($this->google->get_login_url());
	}
	
	/**
	 * validate google auth
	 */
	public function google_auth()
	{
		$this->load->library('google');
		
		$session									= $this->google->validate();
		
		return $this->_validate($session);
	}
	
	/**
	 * redirect to facebook auth url
	 */
	public function facebook()
	{
		redirect($this->fb->get_login_url());
	}
	
	/**
	 * validate facebook auth
	 */
	public function facebook_auth()
	{
		$this->load->library('fb');
		
		$session									= $this->fb->validate();
		
		return $this->_validate($session);
	}
	
	/**
	 * do validation
	 */
	private function _validate($session = null)
	{
		if(defined('DEMO_MODE') && DEMO_MODE)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), base_url());
		}
		
		if($session)
		{
			$query									= $this->model->select
			('
				app__users.user_id,
				app__users.username,
				app__users.group_id,
				app__users.language_id
			')
			->join
			(
				'app__users',
				'app__users.user_id = oauth__login.user_id'
			)
			->get_where
			(
				'oauth__login',
				array
				(
					'oauth__login.service_provider'	=> $session->oauth_provider,
					'oauth__login.access_token'		=> $session->oauth_uid
				)
			)
			->row();
			
			if($query)
			{
				/* set the user credential into session */
				$this->session->set_userdata
				(
					array
					(
						'oauth_uid'					=> $session->oauth_uid,
						'user_id'					=> $query->user_id,
						'username'					=> $query->username,
						'group_id'					=> $query->group_id,
						'language_id'				=> $query->language_id,
						'is_logged'					=> true,
						'session_generated'			=> time()
					)
				);
				
				return throw_exception(301, phrase('welcome_back') . ', <b>' . get_userdata('first_name') . '</b>! ' . phrase('you_were_logged_in'), base_url('dashboard'), true);
			}
			else
			{
				$query								= $this->model->select
				('
					user_id
				')
				->get_where
				(
					'app__users',
					array
					(
						'email'						=> $session->email
					)
				)
				->row();
				
				if($query)
				{
					$this->model->insert
					(
						'oauth__login',
						array
						(
							'user_id'				=> $query->user_id,
							'service_provider'		=> $session->oauth_provider,
							'access_token'			=> $session->oauth_uid,
							'status'				=> 1
						)
					);
					
					return $this->_validate($session);
				}
			}
			
			$photo									= $session->picture;
			$extension								= getimagesize($photo);
			$extension								= image_type_to_extension($extension[2]);
			$upload_name							= sha1(time()) . $extension;
			
			if(copy($photo, UPLOAD_PATH . '/users/' . $upload_name))
			{
				$photo								= $upload_name;
				
				$this->generateThumbnail('users', $upload_name);
			}
			else
			{
				$photo								= 'placeholder.png';
			}
			
			$language_id							= (get_userdata('language_id') ? get_userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
			$default_membership						= (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3);
			
			$this->model->insert
			(
				'app__users',
				array
				(
					'email'							=> $session->email,
					'password'						=> '',
					'username'						=> '',
					'first_name'					=> $session->first_name,
					'last_name'						=> $session->last_name,
					'photo'							=> $photo,
					'phone'							=> '',
					'postal_code'					=> '',
					'language_id'					=> $language_id,
					'group_id'						=> $default_membership,
					'registered_date'				=> date('Y-m-d'),
					'last_login'					=> date('Y-m-d H:i:s'),
					'status'						=> (get_setting('auto_active_registration') ? 1 : 0)
				)
			);
			
			if($this->model->affected_rows() > 0)
			{
				$insert_id							= $this->model->insert_id();
				
				$this->model->insert
				(
					'oauth__login',
					array
					(
						'user_id'					=> $insert_id,
						'service_provider'			=> $session->oauth_provider,
						'access_token'				=> $session->oauth_uid,
						'status'					=> 1
					)
				);
				
				return $this->_validate($session);
			}
		}
	}
	
	/**
	 * generateThumbnail
	 * Generate the thumbnail of uploaded image
	 *
	 * @access		private
	 */
	private function generateThumbnail($type = null, $source = null)
	{
		/* load and initialize the library */
		$this->load->library('image_lib');
		
		/* initialize for thumbnail creation */
		$this->image_lib->initialize
		(
			array
			(
				'image_library'						=> 'gd2',
				'source_image'						=> UPLOAD_PATH . '/' . $type . '/' . $source,
				'new_image'							=> UPLOAD_PATH . '/' . $type . '/thumbs/' . $source,
				'create_thumb'						=> false,
				'width'								=> (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 250),
				'height'							=> (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 250)
			)
		);
		if($this->image_lib->resize())
		{
			$this->image_lib->clear();
			$this->crop($type, $source, 'thumbs');
		}
			
		/* initialize for icon creation */
		$this->image_lib->initialize
		(
			array
			(
				'image_library'						=> 'gd2',
				'source_image'						=> UPLOAD_PATH . '/' . $type . '/' . $source,
				'new_image'							=> UPLOAD_PATH . '/' . $type . '/icons/' . $source,
				'create_thumb'						=> false,
				'width'								=> (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 80),
				'height'							=> (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 80)
			)
		);
		if($this->image_lib->resize())
		{
			$this->image_lib->clear();
			$this->crop($type, $source, 'icons');
		}
	}
	
	/**
	 * crop
	 * Crop the uploaded image
	 *
	 * @access		private
	 */
	private function crop($type = null, $source = null, $size = null)
	{
		if('thumbs' == $size)
		{
			$dimension								= (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 250);
		}
		else
		{
			$dimension								= (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 80);
		}
		
		$config['image_library'] 					= 'gd2';
		$config['source_image'] 					= UPLOAD_PATH . '/' . $type . '/' . $size . '/' . $source;
		$config['new_image'] 						= UPLOAD_PATH . '/' . $type . '/' . $size . '/' . $source;
		$config['create_thumb'] 					= FALSE;
		$config['maintain_ratio'] 					= FALSE;
		$config['width']     						= $dimension;
		$config['height']   						= $dimension;
		list($width, $height)						= getimagesize($config['source_image']);
		
		if($width >= $height)
		{
			/* master dimension in width because the width is greater or equal to height */
			$config['master_dim']					= 'width';
			$config['x_axis']						= 0;
			$config['y_axis']						= -($width - $height) / 2;
		}
		else
		{
			/* master dimension in height because the height is greater width */
			$config['master_dim']					= 'height';
			$config['x_axis']						= -($height - $width) / 2;
			$config['y_axis']						= 0;
		}
		
		/* load and initialize the library */
		$this->load->library('image_lib');
		$this->image_lib->initialize($config);
		$this->image_lib->crop();
		$this->image_lib->clear();
	}
}
