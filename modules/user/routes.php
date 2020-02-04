<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['user/(:any)'] = 'user/$1';
$route['user/(:any)/about'] = 'user/about/$1';
$route['user/(:any)/portfolio'] = 'user/portfolio/$1';