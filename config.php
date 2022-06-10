<?php
/**
 * Configuration file
 * Parameter commented with UPPERCASE must be followed
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 * -------------------------------------------------------------------
 * Sometimes you need a fool to complete a complicated job :)
 * -------------------------------------------------------------------
 */

/**
 * Set the default timezone
 */
define('TIMEZONE', 'Asia/Jakarta');

/**
 * Set the environment
 * Available option: development, production
 */
define('ENVIRONMENT', 'production');

/**
 * Set the default site url
 */
define('BASE_URL', ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

/**
 * Set the application mode
 */
define('DEMO_MODE', false);

/**
 * Set the security hash
 * Make it secret!
 */
define('ENCRYPTION_KEY', 'aksara_]Gb)4uMdG-edH~evm}oT7cqu&S#+/LVHx55PmSX?E]SaL?oe2+ja-g.kmJ*Y.g!G');

/**
 * Cookie name
 * Prevent conflicts between applications built with Aksara
 */
define('COOKIE_NAME', 'aksara_HzRMmAQeOPeHi4Om');

/**
 * Default database connection
 */
define('DB_DSN', '');
define('DB_DRIVER', 'MySQLi');
define('DB_HOSTNAME', 'localhost');
define('DB_PORT', '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'aksaracms');

/**
 * Upload config
 */
define('UPLOAD_PATH', 'uploads'); // NO TRAILING SLASH
define('MAX_UPLOAD_SIZE', 10240); // in kilobytes
define('DOCUMENT_FORMAT_ALLOWED', 'jpg,jpeg,gif,png,pdf,xls,xlsx,doc,docx,csv');
define('IMAGE_FORMAT_ALLOWED', 'jpg,png,gif');
define('IMAGE_DIMENSION', 1024); // in pixels
define('THUMBNAIL_DIMENSION', 256); // in pixels
define('ICON_DIMENSION', 64); // in pixels
