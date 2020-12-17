<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Facebook Login Library
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 * @website		www.dwitrimedia.com
 * @copyright	(c) 2019 - DWITRI Media
 */
 
// Include the autoloader provided in the SDK
require_once __DIR__ . '/facebook/autoload.php';

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
	
class Fb
{
	public function __construct()
	{
		$this->_ci									=& get_instance();
		$this->_client_id							= get_setting('facebook_app_id');
		$this->_client_secret						= $this->_ci->encryption->decrypt(get_setting('facebook_app_secret'));
		
		$this->client								= new Facebook
		(
			array
			(
				'app_id'							=> $this->_client_id,
				'app_secret'						=> $this->_client_secret,
				'default_graph_version'				=> 'v5.0'
			)
		);
		
		$this->helper								= $this->client->getRedirectLoginHelper();
	}
	
	public function get_login_url()
	{
		return $this->helper->getLoginUrl(base_url('auth'), array('email'));
	}
	
	public function validate()
	{
		if($this->_ci->input->get('code') && !$this->_ci->session->userdata('facebook_access_token'))
		{
			$this->_ci->session->set_userdata('facebook_access_token', (string) $this->helper->getAccessToken());
			$oAuth2Client							= $this->client->getOAuth2Client();
			$longLivedAccessToken					= $oAuth2Client->getLongLivedAccessToken($this->_ci->session->userdata('facebook_access_token'));
			
			$this->_ci->session->set_userdata('facebook_access_token', (string) $longLivedAccessToken);
		}
		
		$this->client->setDefaultAccessToken($this->_ci->session->userdata('facebook_access_token'));
		
		$user										= $this->client->get('/me?fields=name,first_name,last_name,email,picture');
		$user										= $user->getGraphNode()->asArray();
		
		return (object) array
		(
			'oauth_provider'						=> 'facebook',
			'oauth_uid'								=> $user['id'],
			'first_name'							=> $user['first_name'],
			'last_name'								=> $user['last_name'],
			'email'									=> $user['email'],
			'locale'								=> 'en',
			'picture'								=> $user['picture']['url']
		);
	}
}
