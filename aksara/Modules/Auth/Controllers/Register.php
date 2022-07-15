<?php

namespace Aksara\Modules\Auth\Controllers;

/**
 * Auth > Register
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Register extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		/* check if user is already signed in */
		if(get_userdata('is_logged'))
		{
			return throw_exception(301, phrase('you_have_been_signed_in'), base_url('dashboard'), true);
		}
		/* check if registration is opened */
		else if(!get_setting('frontend_registration'))
		{
			return throw_exception(403, phrase('the_registration_is_temporary_disabled'), base_url('auth'));
		}
		
		/* unlink old captcha if any */
		if(get_userdata('captcha_file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file')))
		{
			@unlink(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file'));
		}
	}
	
	public function index()
	{
		/* captcha challenge */
		if($this->valid_token(service('request')->getPost('_token')))
		{
			return $this->_validate_form();
		}
		
		$string										= '123456789ABCDEF';
		$length										= 6;
		$captcha									= array();
		
		if(is_writable(UPLOAD_PATH))
		{
			if(!is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha'))
			{
				@mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha', 755, true);
			}
			
			if(is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha') && is_writable(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha'))
			{
				helper('captcha');
				
				$captcha							= create_captcha
				(
					array
					(
						'img_path'					=> UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR,
						'img_url'					=> base_url(UPLOAD_PATH . '/captcha'),
						'img_width'					=> 120,
						'img_height'				=> 30,
						'expiration'				=> 3600,
						'word_length'				=> $length,
						'pool'						=> $string,
						'colors'					=> array
						(
							'background'			=> array(52, 58, 64),
							'border'				=> array(52, 58, 64),
							'grid'					=> array(52, 58, 64),
							'text'					=> array(255, 255, 255)
						)
					)
				);
			}
		}
		
		if(!$captcha)
		{
			$captcha								= array
			(
				'word'								=> substr(str_shuffle(str_repeat($string, ceil($length / strlen($string)))), 1, $length),
				'filename'							=> null
			);
		}
		
		/* set captcha word into session, used to next validation */
		set_userdata
		(
			array
			(
				'captcha'							=> $captcha['word'],
				'captcha_file'						=> $captcha['filename']
			)
		);
		
		$this->set_output
		(
			array
			(
				'captcha'							=> array
				(
					'image'							=> ($captcha['filename'] ? base_url(UPLOAD_PATH . '/captcha/' . $captcha['filename']) : null),
					'string'						=> (!$captcha['filename'] ? $captcha['word'] : null)
				)
			)
		);
		
		$this->set_title(phrase('register_an_account'))
		->set_icon('mdi mdi-account-plus')
		->set_description(phrase('fill_all_the_required_fields_below_to_register_your_account'))
		
		->render();
	}
	
	/**
	 * validate form
	 */
	public function _validate_form()
	{
		if(DEMO_MODE)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), current_page());
		}
		else if(!$this->valid_token(service('request')->getPost('_token')))
		{
			return throw_exception(403, phrase('the_token_you_submitted_has_been_expired_or_you_are_trying_to_bypass_it_from_the_restricted_source'), current_page());
		}
		
		/* load additional library and helper */
		
		$this->form_validation->setRule('first_name', phrase('first_name'), 'required|max_length[32]');
		$this->form_validation->setRule('last_name', phrase('last_name'), 'max_length[32]');
		$this->form_validation->setRule('username', phrase('username'), 'required|alpha_numeric|unique[app__users.username]');
		$this->form_validation->setRule('email', phrase('email_address'), 'required|valid_email|unique[app__users.email]');
		$this->form_validation->setRule('phone', phrase('phone_number'), 'required|min_length[8]|max_length[16]');
		$this->form_validation->setRule('password', phrase('password'), 'required|min_length[6]');
		
		/* validate captcha */
		$this->form_validation->setRule('captcha', phrase('bot_challenge'), 'required|regex_match[/' . get_userdata('captcha') . '/i]');
		
		/* run form validation */
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		/* prepare the insert data */
		$prepare									= array
		(
			'first_name'							=> service('request')->getPost('first_name'),
			'last_name'								=> service('request')->getPost('last_name'),
			'username'								=> service('request')->getPost('username'),
			'email'									=> service('request')->getPost('email'),
			'phone'									=> service('request')->getPost('phone'),
			'password'								=> password_hash(service('request')->getPost('password') . ENCRYPTION_KEY, PASSWORD_DEFAULT),
			'group_id'								=> (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3),
			'language_id'							=> (get_setting('app_language') > 0 ? get_setting('app_language') : 1),
			'registered_date'						=> date('Y-m-d'),
			'last_login'							=> date('Y-m-d H:i:s'),
			'status'								=> (get_setting('auto_active_registration') ? 1 : 0)
		);
		
		/* insert user with safe checkpoint */
		if($this->model->insert('app__users', $prepare, 1))
		{
			$prepare['user_id']						= $this->model->insert_id();
			
			/* unset stored captcha */
			unset_userdata(array('captcha', 'captcha_file'));
			
			if(get_setting('auto_active_registration'))
			{
				$default_membership_group			= (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3);
				
				/* set the user credential into session */
				set_userdata
				(
					array
					(
						'user_id'					=> $prepare['user_id'],
						'group_id'					=> $default_membership_group,
						'language_id'				=> $prepare['language_id'],
						'is_logged'					=> true
					)
				);
				
				/* send email to user */
				$this->_send_welcome_email($prepare);
				
				/* return to previous page */
				return throw_exception(301, phrase('your_account_has_been_registered_successfully'), base_url((service('request')->getGet('redirect') ? service('request')->getGet('redirect') : 'dashboard')), true);
			}
			else
			{
				/* send email to user */
				$this->_send_activation_email($prepare);
				
				/* return to previous page */
				return throw_exception(301, phrase('follow_the_link_we_sent_to_your_email_to_activate_your_account'), base_url('auth', array('activation' => base64_encode(service('encrypter')->encrypt($prepare['user_id'])))));
			}
		}
		else
		{
			/* throw error message */
			return throw_exception(500, phrase('unable_to_register_your_account') . ', ' . phrase('please_try_again_later'));
		}
	}
	
	public function activate()
	{
		$query										= $this->model->select
		('
			app__users.user_id,
			app__users.username,
			app__users.group_id,
			app__users.language_id
		')
		->join
		(
			'app__users',
			'app__users.user_id = app__users_hash.user_id'
		)
		->get_where
		(
			'app__users_hash',
			array
			(
				'app__users_hash.hash'				=> service('request')->getGet('hash')
			),
			1
		)
		->row();
		
		if($query)
		{
			$this->model->update
			(
				'app__users',
				array
				(
					'status'						=> 1
				),
				array
				(
					'user_id'						=> $query->user_id
				)
			);
			
			$this->model->delete
			(
				'app__users_hash',
				array
				(
					'user_id'						=> $query->user_id
				)
			);
			
			/* set the user credential into session */
			set_userdata
			(
				array
				(
					'user_id'						=> $query->user_id,
					'username'						=> $query->username,
					'group_id'						=> $query->group_id,
					'language_id'					=> $query->language_id,
					'is_logged'						=> true,
					'session_generated'				=> time()
				)
			);
			
			return throw_exception(301, phrase('your_account_has_been_successfully_activated'), base_url((service('request')->getGet('redirect') ? service('request')->getGet('redirect') : null)));
		}
		else
		{
			return throw_exception(404, phrase('the_page_you_requested_does_not_exist'), base_url());
		}
	}
	
	private function _send_activation_email($params = array())
	{
		$token										= sha1($params['email'] . time());
		
		$this->model->insert
		(
			'app__users_hash',
			array
			(
				'user_id'							=> $params['user_id'],
				'hash'								=> $token
			)
		);
		
		/**
		 * to working with Google SMTP, make sure to activate less secure apps setting
		 */
		$host										= get_setting('smtp_host');
		$username									= get_setting('smtp_username');
		$password									= (get_setting('smtp_password') ? service('encrypter')->decrypt(base64_decode(get_setting('smtp_password'))) : '');
		$sender_email								= (get_setting('smtp_email_masking') ? get_setting('smtp_email_masking') : (service('request')->getServer('SERVER_ADMIN') ? service('request')->getServer('SERVER_ADMIN') : 'webmaster@' . service('request')->getServer('SERVER_NAME')));
		$sender_name								= (get_setting('smtp_sender_masking') ? get_setting('smtp_sender_masking') : get_setting('app_name'));
		
		$this->email								= \Config\Services::email();
		
		if($host && $username && $password)
		{
			$config['userAgent']       				= 'Aksara';
			$config['protocol']						= 'smtp';
			$config['SMTPCrypto']					= 'ssl';
			$config['SMTPTimeout']					= 5;
			$config['SMTPHost']						= (strpos($host, '://') !== false ? trim(substr($host, strpos($host, '://') + 3)) : $host);
			$config['SMTPPort']						= get_setting('smtp_port');
			$config['SMTPUser']						= $username;
			$config['SMTPPass']						= $password;
		}
		else
		{
			$config['protocol']						= 'mail';
		}
		
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailType']							= 'html'; // text or html
		$config['wordWrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not
		
		$this->email->initialize($config);	
		
		$this->email->setFrom($sender_email, $sender_name);
		$this->email->setTo($params['email']);
		
		$this->email->setSubject(phrase('account_activation'));
		$this->email->setMessage
		('
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>
						' . phrase('account_activation') . '
					</title>
				</head>
				<body>
					<p>
						' . phrase('hi') . ', <b>' . $params['first_name'] . ' ' . $params['last_name'] . '</b>
					</p>
					<p>
						' . phrase('you_are_recently_register_your_account_using_this_email_on_our_website') . ' ' . phrase('your_account_need_to_be_activated') . '
					</p>
					<p>
						<a href="' . current_page('activate', array('hash' => $token)) . '" style="background:#007bff; color:#fff; text-decoration:none; font-weight:bold; border-radius:6px; padding:5px 10px; line-height:3">
							' . phrase('activate_your_account') . '
						</a>
					</p>
					<br />
					<br />
					<p>
						<b>
							' . get_setting('office_name') . '
						</b>
						<br />
						' . get_setting('office_address') . '
						<br />
						' . get_setting('office_phone') . '
					</p>
				</body>
			</html>
		');
		
		if(!$this->email->send())
		{
			//return throw_exception(400, array('message' => $this->email->printDebugger()));
		}
	}
	
	private function _send_welcome_email($params = array())
	{
		/**
		 * to working with Google SMTP, make sure to activate less secure apps setting
		 */
		$host										= get_setting('smtp_host');
		$username									= get_setting('smtp_username');
		$password									= (get_setting('smtp_password') ? service('encrypter')->decrypt(base64_decode(get_setting('smtp_password'))) : '');
		$sender_email								= (get_setting('smtp_email_masking') ? get_setting('smtp_email_masking') : service('request')->getServer('SERVER_ADMIN'));
		$sender_name								= (get_setting('smtp_sender_masking') ? get_setting('smtp_sender_masking') : get_setting('app_name'));
		
		$this->email								= \Config\Services::email();
		
		if($host && $username && $password)
		{
			$config['userAgent']       				= 'Aksara';
			$config['protocol']						= 'smtp';
			$config['SMTPCrypto']					= 'ssl';
			$config['SMTPTimeout']					= 5;
			$config['SMTPHost']						= (strpos($host, '://') !== false ? trim(substr($host, strpos($host, '://') + 3)) : $host);
			$config['SMTPPort']						= get_setting('smtp_port');
			$config['SMTPUser']						= $username;
			$config['SMTPPass']						= $password;
		}
		else
		{
			$config['protocol']						= 'mail';
		}
		
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailType']							= 'html'; // text or html
		$config['wordWrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not
		
		$this->email->initialize($config);	
		
		$this->email->setFrom($sender_email, $sender_name);
		$this->email->setTo($params['email']);
		
		$this->email->setSubject(phrase('account_registered'));
		$this->email->setMessage
		('
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>
						' . phrase('account_registered') . '
					</title>
				</head>
				<body>
					<p>
						' . phrase('hi') . ', <b>' . $params['first_name'] . ' ' . $params['last_name'] . '</b>
					</p>
					<p>
						<b>
							' . phrase('congratulations') . ',
						</b>
						<br />
						' . phrase('your_account_was_successfully_registered_to_our_website') . ' ' . phrase('you_can_use_your_email_or_username_to_sign_in_to_your_dashboard') . '
					</p>
					<br />
					<br />
					<p>
						<b>
							' . get_setting('office_name') . '
						</b>
						<br />
						' . get_setting('office_address') . '
						<br />
						' . get_setting('office_phone') . '
					</p>
				</body>
			</html>
		');
		
		if(!$this->email->send())
		{
			//return throw_exception(400, array('message' => $this->email->printDebugger()));
		}
	}
}
