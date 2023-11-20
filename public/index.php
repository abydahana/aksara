<?php

// Check PHP version.
$minPhpVersion = '7.4'; // If you update this, don't forget to update `spark`.

if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit('<center>' . $message . '</center>');
} elseif (! in_array('intl', array_map('strtolower', get_loaded_extensions()))) {
    exit('<center>You need to enable the INTL module on your server. <a href="//www.google.com/search?q=install+intl+extension" target="_blank"><b>Click here</b></a> to install the INTL extension on your server.</center>');
} elseif (! is_dir('../vendor')) {
    exit('<center>Please run "<code style="color:green">composer install</code>" from "<code style="color:red">' . dirname(__DIR__) . '</code>" to fetch the required dependencies before we start the installation wizard.<br /><a href="//getcomposer.org/download" target="_blank"><b>Click here</b></a> to download the composer if it\'s not yet installed on your web server.</center>');
} elseif (
    ! is_writable('../writable') || 
    ! is_writable('../writable/session') || 
    ! is_writable('../public/uploads')) {
    exit('<center>You need to make the following directory and it\'s subdirectories to be writable recursively: ' . (! is_writable('../writable') || ! is_writable('../writable/session') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable' . '</code>' : null) . (! is_writable('uploads') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . '</code>' : null) . '<br />Please <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>click here</b></a> to get support related to this error.</center>');
}

// check if configuration file already exists
if (! file_exists('../config.php')) {
    if (! is_dir('../install')) {
        exit('<center>The config file or installer does not exists!</center>');
    }

    define('BASE_URL', ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

    $config_path = 'install';
} else {
    // include the configuration file
    require_once '../config.php';

    $config_path = 'aksara';
}

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */
// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../' . $config_path . '/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

// Define ENVIRONMENT
if (! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', env('CI_ENVIRONMENT', 'production'));
}

// Load Config Cache
// $factoriesCache = new \CodeIgniter\Cache\FactoriesCache();
// $factoriesCache->load('config');
// ^^^ Uncomment these lines if you want to use Config Caching.

/*
 * ---------------------------------------------------------------
 * GRAB OUR CODEIGNITER INSTANCE
 * ---------------------------------------------------------------
 *
 * The CodeIgniter class contains the core functionality to make
 * the application run, and does all the dirty work to get
 * the pieces all working together.
 */

$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is set up, it's time to actually fire
 * up the engines and make this app do its thang.
 */

$app->run();

// Save Config Cache
// $factoriesCache->save('config');
// ^^^ Uncomment this line if you want to use Config Caching.

// Exits the application, setting the exit code for CLI-based applications
// that might be watching.
exit(EXIT_SUCCESS);
