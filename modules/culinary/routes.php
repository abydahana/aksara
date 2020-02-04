<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['culinary/search'] = 'culinary/search';
$route['culinary/tags'] = 'culinary/tags';
$route['culinary/(:any)'] = 'culinary/detail/$1';