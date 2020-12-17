<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Google Login Library
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 * @website		www.dwitrimedia.com
 * @copyright	(c) 2019 - DWITRI Media
 */
class Google
{
	public function __construct()
	{
		$this->_ci									=& get_instance();
		$this->_client_id							= get_setting('google_client_id');
		$this->_client_secret						= $this->_ci->encryption->decrypt(get_setting('google_client_secret'));
		
		require_once('google/Google_Client.php');
		require_once('google/contrib/Google_Oauth2Service.php');
		
		$this->client								= new Google_Client();
		$this->client->setClientId($this->_client_id);
		$this->client->setClientSecret($this->_client_secret);
		$this->client->setRedirectUri(base_url('auth'));
		$this->client->setScopes
		(
			array
			(
				'https://www.googleapis.com/auth/userinfo.email',
				'https://www.googleapis.com/auth/userinfo.profile'
			)
		);
	}
	
	/**
	 * getting the login url
	 */
	public function get_login_url()
	{
		return $this->client->createAuthUrl();
	}
	
	/**
	 * validate session
	 */
	public function validate()
	{
		$user										= new Google_Oauth2Service($this->client);
		
		if($this->_ci->input->get('code') && !$this->_ci->session->userdata('access_token'))
		{
			$this->client->authenticate($this->_ci->input->get('code'));
			$this->_ci->session->set_userdata('access_token', $this->client->getAccessToken());
		}
		
		$this->client->setAccessToken($this->_ci->session->userdata('access_token'));
		
		$user										= $user->userinfo->get();
		
		return (object) array
		(
			'oauth_provider'						=> 'google',
			'oauth_uid'								=> $user['id'],
			'first_name'							=> $user['given_name'],
			'last_name'								=> $user['family_name'],
			'email'									=> $user['email'],
			'locale'								=> $user['locale'],
			'picture'								=> $user['picture']
		);
	}
	
	/**
	 * revoke token
	 */
	public function revokeToken()
	{
		return $this->client->revokeToken();
	}
}
