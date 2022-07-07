<?php

namespace Aksara\Libraries;

/**
 * Facebook Login Library
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

use Facebook\Facebook as BaseFacebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
	
class Facebook
{
	private $_client_secret;
	
	public function __construct()
	{
		$this->_client_id							= get_setting('facebook_app_id');
		
		try
		{
			// try to decrypting the app secret
			$this->_client_secret					= service('encrypter')->decrypt(base64_decode(get_setting('facebook_app_secret')));
		}
		catch(\Throwable $e)
		{
		}
		
		$this->client								= new BaseFacebook
		(
			array
			(
				'app_id'							=> ($this->_client_id ? $this->_client_id : 'no_key'),
				'app_secret'						=> ($this->_client_secret ? $this->_client_secret : 'no_hash'),
				'default_graph_version'				=> 'v12.0'
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
		if(service('request')->getGet('code') && !get_userdata('access_token'))
		{
			set_userdata('access_token', (string) $this->helper->getAccessToken());
			$oAuth2Client							= $this->client->getOAuth2Client();
			$longLivedAccessToken					= $oAuth2Client->getLongLivedAccessToken(get_userdata('access_token'));
			
			set_userdata('access_token', (string) $longLivedAccessToken);
		}
		
		$this->client->setDefaultAccessToken(get_userdata('access_token'));
		
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
