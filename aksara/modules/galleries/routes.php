<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['galleries/(:any)'] = 'galleries/category/$1';
$route['galleries/(:any)/(:any)'] = 'galleries/read/$1/$2';