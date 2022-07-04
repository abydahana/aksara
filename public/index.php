<?php

if(version_compare(PHP_VERSION, '7.3', '<'))
{
	exit('<center>You need to update your PHP version to 7.3 or newer!</center>');
}
else if(!is_dir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor'))
{
	exit('<center>Please run "<code style="color:green">composer install</code>" from "<code style="color:red">' . dirname(__DIR__) . '</code>" to fetch the required dependencies before we start the installation wizard.<br /><a href="//getcomposer.org/download" target="_blank"><b>Click here</b></a> to download the composer if it\'s not yet installed on your web server.</center>');
}
else if(!in_array('intl', array_map('strtolower', get_loaded_extensions())))
{
	exit('<center>You need to enable the INTL module on your server. <a href="//www.google.com/search?q=install+intl+extension" target="_blank"><b>Click here</b></a> to install the INTL extension on your server.</center>');
}
else if(!is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable') || !is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'session') || !is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads'))
{
	exit('<center>You need to make the following directory and it\'s subdirectories to be writable recursively: ' . (!is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable') || !is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'session') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable' . '</code>' : null) . (!is_writable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . '</code>' : null) . '<br />Please <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>click here</b></a> to get support related to this error.</center>');
}

// check if configuration file already exists
if(!file_exists('../config.php'))
{
	define('BASE_URL', ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
}
else
{
	// include the configuration file
	require_once '../config.php';
}

// Valid PHP Version?
$minPHPVersion = '7.3';
if (version_compare(PHP_VERSION, $minPHPVersion, '<'))
{
	die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . PHP_VERSION);
}
unset($minPHPVersion);

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
require realpath(FCPATH . (file_exists('../config.php') ? '../aksara/Config/Paths.php' : '../install/Config/Paths.php')) ?: FCPATH . (file_exists('../config.php') ? '../aksara/Config/Paths.php' : '../install/Config/Paths.php');
// ^^^ Change this if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app       = require realpath($bootstrap) ?: $bootstrap;

/*
 *---------------------------------------------------------------
 * SCRIPT TAMBAHAN
 *---------------------------------------------------------------
 * Mengacu pada file Codeigniter 4 relase terbaru: (https://github.com/codeigniter4/CodeIgniter4/blob/develop/public/index.php)
 * agar support untuk PHP Built-in web server ( php -S localhost:8000 )
 */
$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();


/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
