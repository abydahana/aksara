<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['recipes/search'] = 'recipes/search';
$route['recipes/tags'] = 'recipes/tags';
$route['recipes/(:any)'] = 'recipes/detail/$1';