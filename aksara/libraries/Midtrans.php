<?php
/** 
 * Check PHP version.
 */
if (version_compare(PHP_VERSION, '5.4', '<')) {
    throw new Exception('PHP version >= 5.4 required');
}

// Check PHP Curl & json decode capabilities.
if (!function_exists('curl_init') || !function_exists('curl_exec')) {
    throw new Exception('Midtrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Midtrans needs the JSON PHP extension.');
}

// Configurations
require_once 'Midtrans/Config.php';

// Midtrans API Resources
require_once 'Midtrans/Transaction.php';

// Plumbing
require_once 'Midtrans/ApiRequestor.php';
require_once 'Midtrans/SnapApiRequestor.php';
require_once 'Midtrans/Notification.php';
require_once 'Midtrans/CoreApi.php';
require_once 'Midtrans/Snap.php';

// Sanitization
require_once 'Midtrans/Sanitizer.php';

class Midtrans
{
	function __construct($params = array())
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
}