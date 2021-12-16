<?php
if(version_compare(PHP_VERSION, '7.3', '<'))
{
	exit('<center>You need to update your PHP version to 7.3 or newer!</center>');
}
else if(!is_dir(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor'))
{
	exit('<center>Please run "<code style="color:green">composer install</code>" from "<code style="color:red">' . dirname(dirname(__DIR__)) . '</code>" to fetch the required dependencies before we start the installation wizard.<br /><a href="//getcomposer.org/download" target="_blank"><b>Click here</b></a> to download the composer if it\'s not yet installed on your web server.</center>');
}
else if(!in_array('intl', array_map('strtolower', get_loaded_extensions())))
{
	exit('<center>You need to enable the INTL module on your server. <a href="//www.google.com/search?q=install+intl+extension" target="_blank"><b>Click here</b></a> to install the INTL extension on your server.</center>');
}
else if(!is_writable(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'writable') || !is_writable(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads'))
{
	exit('<center>You need to make the following directory and it\'s subdirectories to be writable: ' . (!is_writable(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'writable') || !is_writable(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'cache') ? '<code style="color:red">' . dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'writable' . '</code>' : null) . ' and ' . (!is_writable(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads') ? '<code style="color:red">' . dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . '</code>' : null) . '<br>Please <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>click here</b></a> to get support related to this error.</center>');
}

define('ENVIRONMENT', 'development');

// Set base URL
define('BASE_URL', ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http') . '://'. $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
$pathsConfig = FCPATH . 'Config/Paths.php';
// ^^^ Change this if you move your application folder
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();

// Location of the framework bootstrap file.
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app       = require realpath($bootstrap) ?: $bootstrap;

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
