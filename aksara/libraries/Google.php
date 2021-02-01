<?php namespace Aksara\Libraries;
/**
 * Google Login Library
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Google
{
	public function __construct()
	{
		$this->_client_id							= get_setting('google_client_id');
		$this->_client_secret						= service('encrypter')->decrypt(base64_decode(get_setting('google_client_secret')));
		
		$this->client								= new \Google_Client();
		
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
		$user										= new \Google_Service_Oauth2($this->client);
		
		if(service('request')->getGet('code') && !get_userdata('access_token'))
		{
			$this->client->authenticate(service('request')->getGet('code'));
			
			set_userdata('access_token', $this->client->getAccessToken());
		}
		
		$this->client->setAccessToken(get_userdata('access_token'));
		
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
