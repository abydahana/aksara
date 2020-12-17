<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Auth > Register
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Register extends Aksara
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
		if($this->session->userdata('captcha_file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . $this->session->userdata('captcha_file')))
		{
			@unlink(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . $this->session->userdata('captcha_file'));
		}
	}
	
	public function index()
	{
		/* captcha challenge */
		if(!$this->input->post('_token'))
		{
			$string									= '123456789ABCDEF';
			$length									= 6;
			$captcha								= array();
			
			if(is_writable(UPLOAD_PATH))
			{
				if(is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha') && is_writable(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha'))
				{
					$this->load->helper('captcha');
					
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
				else
				{
					@mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha');
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
			$this->session->set_userdata
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
		if(defined('DEMO_MODE') && DEMO_MODE)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), base_url());
		}
		
		/* load additional library and helper */
		$this->load->library('form_validation');
		$this->load->helper('security');
		
		$this->form_validation->set_rules('first_name', phrase('first_name'), 'required|max_length[32]');
		$this->form_validation->set_rules('username', phrase('username'), 'required|alpha_numeric|is_unique[app__users.username]');
		$this->form_validation->set_rules('email', phrase('email_address'), 'required|valid_email|is_unique[app__users.email]');
		$this->form_validation->set_rules('phone', phrase('phone_number'), 'required|min_length[8]|max_length[16]');
		$this->form_validation->set_rules('password', phrase('password'), 'required|min_length[6]');
		
		/* validate captcha */
		$this->form_validation->set_rules('captcha', phrase('bot_challenge'), 'required|regex_match[/' . $this->session->userdata('captcha') . '/i]');
		
		/* run form validation */
		if($this->form_validation->run() === false)
		{
			return throw_exception(400, $this->form_validation->error_array());
		}
		
		/* prepare the insert data */
		$prepare									= array
		(
			'first_name'							=> $this->input->post('first_name'),
			'username'								=> $this->input->post('username'),
			'email'									=> $this->input->post('email'),
			'phone'									=> $this->input->post('phone'),
			'password'								=> password_hash($this->input->post('password') . SALT, PASSWORD_DEFAULT),
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
			$this->session->unset_userdata(array('captcha', 'captcha_file'));
			
			if(get_setting('auto_active_registration'))
			{
				$default_membership_group			= (get_setting('default_membership_group') ? get_setting('default_membership_group') : 3);
				
				/* set the user credential into session */
				$this->session->set_userdata
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
				$this->_send_welcome_email($this->input->post('email'), $this->input->post('username'), $this->input->post('first_name'), $this->input->post('last_name'));
				
				/* return to previous page */
				return throw_exception(301, phrase('your_account_has_been_registered_successfully'), base_url('dashboard'), true);
			}
			else
			{
				/* send email to user */
				$this->_send_activation_email($insert_id, $this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'));
				
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
			app__users.user_id
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
				'app__users_hash.hash'				=> $this->input->get('hash')
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
			
			return throw_exception(301, phrase('your_account_has_been_successfully_activated'), base_url('auth', array('hash' => null)));
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
		$this->load->library('email');
		
		$config['useragent']       					= 'Aksara';
		$config['protocol']							= 'smtp';
		$config['smtp_host']						= get_setting('smtp_host');
		$config['smtp_port']						= get_setting('smtp_port');
		$config['smtp_user']						= get_setting('smtp_username');
		$config['smtp_pass']						= $this->encryption->decrypt(get_setting('smtp_password'));
		$config['smtp_timeout']						= '7';
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailtype']							= 'html'; // text or html
		$config['wordwrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not     
		
		$this->email->initialize($config);		
		
		$this->email->from(get_setting('smtp_email_masking'), get_setting('smtp_sender_masking'));
		$this->email->to($email);
		
		$this->email->subject(phrase('account_activation'));
		$this->email->message
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
						' . phrase('regards') . ',
					</p>
					<p>
						<b>
							' . phrase('notification_system') . '
						</b>
						<br />
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
		$this->load->library('email');
		
		$config['useragent']       					= 'Aksara';
		$config['protocol']							= 'smtp';
		$config['smtp_host']						= get_setting('smtp_host');
		$config['smtp_port']						= get_setting('smtp_port');
		$config['smtp_user']						= get_setting('smtp_username');
		$config['smtp_pass']						= $this->encryption->decrypt(get_setting('smtp_password'));
		$config['smtp_timeout']						= '7';
		$config['charset']							= 'utf-8';
		$config['newline']							= "\r\n";
		$config['mailtype']							= 'html'; // text or html
		$config['wordwrap']							= true;
		$config['validation']						= true; // bool whether to validate email or not     
		
		$this->email->initialize($config);		
		
		$this->email->from(get_setting('smtp_email_masking'), get_setting('smtp_sender_masking'));
		$this->email->to($email);
		
		$this->email->subject(phrase('account_registration_successfully'));
		$this->email->message
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
						' . phrase('regards') . ',
					</p>
					<p>
						<b>
							' . phrase('notification_system') . '
						</b>
						<br />
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
