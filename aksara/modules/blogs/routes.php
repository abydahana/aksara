<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * extra routes
 */

$route['blogs/search'] = 'blogs/search';
$route['blogs/tags'] = 'blogs/tags';
$route['blogs/(:any)'] = 'blogs/category/$1';
$route['blogs/(:any)/(:any)'] = 'blogs/read/$1/$2';