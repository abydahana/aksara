<?php namespace Aksara\Modules\Auth\Controllers;
/**
 * Auth > Forgot
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Forgot extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		/* check if use is already signed in */
		if(get_userdata('is_logged'))
		{
			return throw_exception(301, phrase('you_have_been_signed_in'), base_url('dashboard'), true);
		}
		
		$this->set_title(phrase('reset_password'))
		->set_icon('mdi mdi-key-change')
		->set_description(phrase('reset_your_password_and_request_new_one'))
		
		->form_callback('_validate_form')
		
		->render();
	}
	
	public function _validate_form()
	{
		error_reporting(0); // prevent the PHP throw errors before exception
		
		$this->form_validation->setRule('username', phrase('username_or_email'), 'required');
		
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		$query										= $this->model->select
		('
			user_id,
			email,
			first_name,
			last_name,
			status
		')
		->where('username', service('request')->getPost('username'))
		->or_where('email', service('request')->getPost('username'))
		->get_where
		(
			'app__users',
			array
			(
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(400, array('username' => phrase('the_username_or_email_you_entered_does_not_registered')));
		}
		elseif(!$query->status)
		{
			return throw_exception(400, array('username' => phrase('your_account_is_temporary_disabled_or_not_yet_activated')));
		}
		
		$token										= sha1(service('request')->getPost('username') . time());
		
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
		$this->email->setTo($query->email);
		
		$this->email->setSubject(phrase('reset_password'));
		$this->email->setMessage
		('
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>
						' . phrase('request_new_password') . '
					</title>
				</head>
				<body>
					<p>
						' . phrase('hi') . ', <b>' . $query->first_name . ' ' . $query->last_name . '</b>
					</p>
					<p>
						' . phrase('someone_is_recently_asked_to_reset_the_password_for_an_account_linked_to_your_email') . ' ' . phrase('please_click_the_button_below_to_reset_your_password') . '
					</p>
					<p>
						<a href="' . current_page('reset', array('hash' => $token)) . '" style="background:#007bff; color:#fff; text-decoration:none; font-weight:bold; border-radius:6px; padding:5px 10px; line-height:3">
							' . phrase('reset_password') . '
						</a>
					</p>
					<p>
						' . phrase('if_this_action_is_not_requested_by_yourself_you_can_just_ignore_this_email') . '
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
			//echo $this->email->printDebugger(); exit;
			return throw_exception(400, array('message' => $this->email->printDebugger()));
		}
		
		$this->model->insert
		(
			'app__users_hash',
			array
			(
				'user_id'							=> $query->user_id,
				'hash'								=> $token
			)
		);
		
		return throw_exception(301, phrase('the_password_reset_link_has_been_sent_to') . ' ' . $query->email, base_url('auth'));
	}
	
	public function reset()
	{
		$query										= $this->model->get_where
		(
			'app__users_hash',
			array
			(
				'hash'								=> service('request')->getGet('hash')
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_page_you_requested_was_not_found_or_it_is_already_removed'), base_url());
		}
		
		$this->set_title(phrase('reset_password'))
		->set_icon('mdi mdi-key-change')
		->set_description(phrase('reset_your_password_and_request_new_one'))
		
		->form_callback('_reset_password')
		
		->render(null, 'reset');
	}
	
	public function _reset_password()
	{
		error_reporting(0); // prevent the PHP throw errors before exception
		
		$this->form_validation->setRule('password', phrase('new_password'), 'required');
		$this->form_validation->setRule('confirm_password', phrase('password_confirmation'), 'required|matches[password]');
		
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		$query										= $this->model->select
		('
			app__users.user_id,
			app__users.email,
			app__users.first_name,
			app__users.last_name,
			app__users.status
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
		
		if(!$query)
		{
			return throw_exception(400, array('password' => phrase('your_password_has_been_reset_recently')));
		}
		elseif(!$query->status)
		{
			return throw_exception(400, array('password' => phrase('your_account_is_temporary_disabled_or_not_yet_activated')));
		}
		
		$this->model->update
		(
			'app__users',
			array
			(
				'password'							=> password_hash(service('request')->getPost('password') . ENCRYPTION_KEY, PASSWORD_DEFAULT)
			),
			array
			(
				'user_id'							=> $query->user_id
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
		$this->email->setTo($query->email);
		
		$this->email->setSubject(phrase('password_reset_successfully'));
		$this->email->setMessage
		('
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>
						' . phrase('password_reset_successfully') . '
					</title>
				</head>
				<body>
					<p>
						' . phrase('hi') . ', <b>' . $query->first_name . ' ' . $query->last_name . '</b>
					</p>
					<p>
						' . phrase('your_password_has_been_reset_successfully') . ' ' . phrase('now_you_can_sign_in_to_our_website_with_your_new_password') . '
					</p>
					<p>
						' . phrase('please_contact_us_directly_if_you_still_cannot_signing_in') . '
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
			//echo $this->email->printDebugger(); exit;
			return throw_exception(400, array('message' => $this->email->printDebugger()));
		}
		
		$this->model->delete
		(
			'app__users_hash',
			array
			(
				'user_id'							=> $query->user_id
			)
		);
		
		return throw_exception(301, phrase('your_password_has_been_reset_successfully'), base_url('auth', array('hash' => null)));
	}
}
