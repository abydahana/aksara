<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['travelling/search'] = 'travelling/search';
$route['travelling/tags'] = 'travelling/tags';
$route['travelling/(:any)'] = 'travelling/detail/$1';