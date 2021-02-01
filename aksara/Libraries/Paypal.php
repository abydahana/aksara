<?php namespace Aksara\Libraries;
/**
 * PayPal Library
 * A connector to integrate payment within PayPal
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Paypal
{
	public function __construct($params = array())
	{
		$this->_api_context							= new \PayPal\Rest\ApiContext
		(
			new \PayPal\Auth\OAuthTokenCredential
			(
				(isset($params['client_id']) ? $params['client_id'] : null),
				(isset($params['client_secret']) ? $params['client_secret'] : null)
			)
		);
	}
	
	public function execute()
	{
		try
		{
			$payment->create($this->_api_context);
			
			return $payment;
		}
		catch (\PayPal\Exception\PayPalConnectionException $e)
		{
			// This will print the detailed information on the exception.
			//REALLY HELPFUL FOR DEBUGGING
			return $e->getData();
		}
	}
}
