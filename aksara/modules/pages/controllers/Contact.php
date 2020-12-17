<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pages > Contact
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Contact extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		if($this->input->post('_token'))
		{
			return $this->_send_message();
		}
		
		$this->set_title(phrase('contact_us'))
		->set_icon('mdi mdi-phone-classic')
		->set_description(phrase('including_the_credits_of_external_resources'))
		->render();
	}
	
	public function _send_message()
	{
		error_reporting(0); // prevent the PHP throw errors before exception
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('full_name', phrase('full_name'), 'required');
		$this->form_validation->set_rules('email', phrase('email'), 'required|valid_email');
		$this->form_validation->set_rules('subject', phrase('subject'), 'required');
		$this->form_validation->set_rules('messages', phrase('messages'), 'required');
		$this->form_validation->set_rules('copy', phrase('send_copy'), 'is_boolean');
		
		if($this->form_validation->run() === false)
		{
			return throw_exception(400, $this->form_validation->error_array());
		}
		
		$this->model->insert
		(
			'inquiries',
			array
			(
				'sender_email'						=> $this->input->post('email'),
				'sender_full_name'					=> $this->input->post('full_name'),
				'subject'							=> $this->input->post('subject'),
				'messages'							=> $this->input->post('messages'),
				'timestamp'							=> date('Y-m-d H:i:s')
			)
		);
		
		if($this->input->post('copy'))
		{
			/**
			 * to working with Google SMTP, make sure to activate less secure apps setting
			 */
			$this->load->library('email');
			
			$config['useragent']       				= 'Aksara';
			$config['protocol']						= 'smtp';
			$config['smtp_host']					= get_setting('smtp_host');
			$config['smtp_port']					= get_setting('smtp_port');
			$config['smtp_user']					= get_setting('smtp_username');
			$config['smtp_pass']					= $this->encryption->decrypt(get_setting('smtp_password'));
			$config['smtp_timeout']					= '7';
			$config['charset']						= 'utf-8';
			$config['newline']						= "\r\n";
			$config['mailtype']						= 'text'; // text or html
			$config['wordwrap']						= true;
			$config['validation']					= true; // bool whether to validate email or not     
			
			$this->email->initialize($config);		
			
			$this->email->from(get_setting('smtp_email_masking'), get_setting('smtp_sender_masking'));
			$this->email->to($this->input->post('email'));
			
			$this->email->subject($this->input->post('subject'));
			$this->email->message($this->input->post('messages'));
			
			if(!$this->email->send())
			{
				//echo $this->email->print_debugger(); exit;
				return throw_exception(400, array('message' => $this->email->print_debugger()));
			}
		}
		
		return throw_exception(301, phrase('your_inquiry_has_been_sent'), current_page());
	}
}
