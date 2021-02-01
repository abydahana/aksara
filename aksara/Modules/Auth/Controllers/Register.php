<?php namespace Aksara\Modules\Auth\Controllers;
/**
 * Auth > Register
 *
 * @author			Aby Dahana
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
		elseif(!get_setting('frontend_registration'))
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
		if(!service('request')->getPost('_token'))
		{
			$string									= '123456789ABCDEF';
			$length									= 6;
			$captcha								= array();
			
			if(is_writable(UPLOAD_PATH))
			{
				if(!is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha'))
				{
					@mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha', 755, true);
				}
				
				if(is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha') && is_writable(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha'))
				{
					helper('captcha');
					
					$captcha						= create_captcha
					(
						array
						(
							'img_path'				=> UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR,
							'img_url'				=> base_url(UPLOAD_PATH . '/captcha'),
							'img_width'				=> 120,
							'img_height'			=> 30,
							'expiration'			=> 3600,
							'word_length'			=> $length,
							'pool'					=> $string,
							'colors'				=> array
							(
								'background'		=> array(52, 58, 64),
								'border'			=> array(52, 58, 64),
								'grid'				=> array(52, 58, 64),
								'text'				=> array(255, 255, 255)
							)
						)
					);
				}
			}
			
			if(!$captcha)
			{
				$captcha							= array
				(
					'word'							=> substr(str_shuffle(str_repeat($string, ceil($length / strlen($string)))), 1, $length),
					'filename'						=> null
				);
			}
			
			/* set captcha word into session, used to next validation */
			set_userdata
			(
				array
				(
					'captcha'						=> $captcha['word'],
					'captcha_file'					=> $captcha['filename']
				)
			);
			
			$this->set_output
			(
				array
				(
					'captcha'						=> array
					(
						'image'						=> ($captcha['filename'] ? base_url(UPLOAD_PATH . '/captcha/' . $captcha['filename']) : null),
						'string'					=> (!$captcha['filename'] ? $captcha['word'] : null)
					)
				)
			);
		}
		
		$this->set_title(phrase('register_an_account'))
		->set_icon('mdi mdi-account-plus')
		->set_description(phrase('fill_all_the_required_field_below_to_take_a_new_account'))
		->form_callback('_validate_form')
		
		->render();
	}
	
	/**
	 * validate form
	 */
	public function _validate_form()
	{
		if(DEMO_MODE)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), base_url());
		}
		
		/* load additional library and helper */
		
		$this->form_validation->setRule('first_name', phrase('first_name'), 'required|max_length[32]');
		$this->form_validation->setRule('last_name', phrase('last_name'), 'max_length[32]');
		$this->form_validation->setRule('username', phrase('username'), 'required|alpha_numeric|is_unique[app__users.username]');
		$this->form_validation->setRule('email', phrase('email_address'), 'required|valid_email|is_unique[app__users.email]');
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
			$insert_id								= $this->model->insert_id();
			
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
						'user_id'					=> $insert_id,
						'group_id'					=> $default_membership_group,
						'language_id'				=> $prepare['language_id'],
						'is_logged'					=> true
					)
				);
				
				/* send email to user */
				$this->_send_welcome_email(service('request')->getPost('email'), service('request')->getPost('username'), service('request')->getPost('first_name'), service('request')->getPost('last_name'));
				
				/* return to previous page */
				return throw_exception(301, phrase('your_account_has_been_registered_successfully'), base_url('dashboard'), true);
			}
			else
			{
				/* send email to user */
				$this->_send_activation_email($insert_id, service('request')->getPost('email'), service('request')->getPost('first_name'), service('request')->getPost('last_name'));
				
				/* return to previous page */
				return throw_exception(301, phrase('follow_the_link_we_sent_to_your_email_to_activate_your_account'), base_url('auth'));
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
			
			return throw_exception(301, phrase('your_account_has_been_successfully_activated'), base_url());
		}
		else
		{
			return throw_exception(404, phrase('the_page_you_requested_was_not_found_or_it_is_already_removed'), base_url());
		}
	}
	
	private function _send_activation_email($user_id = 0, $email = null, $first_name = null, $last_name = null)
	{
		error_reporting(0); // prevent the PHP throw errors before exception
		
		$token										= sha1($email . time());
		
		$this->model->insert
		(
			'app__users_hash',
			array
			(
				'user_id'							=> $user_id,
				'hash'								=> $token
			)
		);
		
		/**
		 * to working with Google SMTP, make sure to activate less secure apps setting
		 */
		$this->email								= \Config\Services::email();
		
		$host										= get_setting('smtp_host');
		
		$config['userAgent']       					= 'Aksara';
		$config['protocol']							= 'smtp';
		$config['SMTPCrypto']						= 'ssl';
		$config['SMTPHost']							= (strpos($host, '://') !== false ? trim(substr($host, strpos($host, '://') + 3)) : $host);
		$config['SMTPPort']							= get_setting('smtp_port');
		$config['SMTPUser']							= get_setting('smtp_username');
		$config['SMTPPass']							= service('encrypter')->decrypt(base64_decode(get_setting('smtp_password')));
		$config['SMTPTimeout']						= 5;
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailType']							= 'html'; // text or html
		$config['wordWrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not
		
		$this->email->initialize($config);		
		
		$this->email->setFrom(get_setting('smtp_email_masking'), get_setting('smtp_sender_masking'));
		$this->email->setTo($email);
		
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
						' . phrase('hi') . ', <b>' . $first_name . ' ' . $last_name . '</b>
					</p>
					<p>
						' . phrase('you_are_recently_registered_your_account_using_this_email_on_our_website') . ' ' . phrase('you_need_to_activate_your_account_before_you_can_signing_in') . '
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
		}
	}
	
	private function _send_welcome_email($email = null, $username = null, $first_name = null, $last_name = null)
	{
		error_reporting(0); // prevent the PHP throw errors before exception
		
		/**
		 * to working with Google SMTP, make sure to activate less secure apps setting
		 */
		$this->email								= \Config\Services::email();
		
		$host										= get_setting('smtp_host');
		
		$config['userAgent']       					= 'Aksara';
		$config['protocol']							= 'smtp';
		$config['SMTPCrypto']						= 'ssl';
		$config['SMTPHost']							= (strpos($host, '://') !== false ? trim(substr($host, strpos($host, '://') + 3)) : $host);
		$config['SMTPPort']							= get_setting('smtp_port');
		$config['SMTPUser']							= get_setting('smtp_username');
		$config['SMTPPass']							= service('encrypter')->decrypt(base64_decode(get_setting('smtp_password')));
		$config['SMTPTimeout']						= 5;
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailType']							= 'html'; // text or html
		$config['wordWrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not
		
		$this->email->initialize($config);		
		
		$this->email->setFrom(get_setting('smtp_email_masking'), get_setting('smtp_sender_masking'));
		$this->email->setTo($email);
		
		$this->email->setSubject(phrase('account_registration_successfully'));
		$this->email->setMessage
		('
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>
						' . phrase('account_registration_successfully') . '
					</title>
				</head>
				<body>
					<p>
						' . phrase('hi') . ', <b>' . $first_name . ' ' . $last_name . '</b>
					</p>
					<p>
						<b>
							' . phrase('congratulations') . '
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
		}
	}
}
