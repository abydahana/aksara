<?php namespace Aksara\Libraries;
/**
 * Facebook Login Library
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

// Include required libraries
use Facebook\Facebook as BaseFacebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
	
class Facebook
{
	public function __construct()
	{
		$this->_client_id							= get_setting('facebook_app_id');
		$this->_client_secret						= service('encrypter')->decrypt(base64_decode(get_setting('facebook_app_secret')));
		
		$this->client								= new BaseFacebook
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
		if(service('request')->getGet('code') && !get_userdata('facebook_access_token'))
		{
			set_userdata('facebook_access_token', (string) $this->helper->getAccessToken());
			$oAuth2Client							= $this->client->getOAuth2Client();
			$longLivedAccessToken					= $oAuth2Client->getLongLivedAccessToken(get_userdata('facebook_access_token'));
			
			set_userdata('facebook_access_token', (string) $longLivedAccessToken);
		}
		
		$this->client->setDefaultAccessToken(get_userdata('facebook_access_token'));
		
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
