<?php namespace Aksara\Libraries;
/**
 * Midtrans Payment Library
 * A connector to integrate payment withing Midtrans
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Midtrans
{
	public function __construct($params = array())
	{
		// Set your Merchant Server Key
		\Midtrans\Config::$serverKey = (isset($params['server_key']) ? $params['server_key'] : null);
		// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
		\Midtrans\Config::$isProduction = (isset($params['production']) ? $params['production'] : false);
		// Set sanitization on (default)
		\Midtrans\Config::$isSanitized = (isset($params['sanitized']) ? $params['sanitized'] : true);
		// Set 3DS transaction for credit card to true
		\Midtrans\Config::$is3ds = (isset($params['3ds']) ? $params['3ds'] : true);
	}
	
	public function snap($params = array())
	{
		try
		{
			$paymentUrl								= \Midtrans\Snap::createTransaction($params)->redirect_url;
			
			return throw_exception(301, null, $paymentUrl, true);
		}
		catch (\Exception $e)
		{
			return throw_exception(500, $e->getMessage());
		}
	}
}
