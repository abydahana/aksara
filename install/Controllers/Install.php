<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

namespace App\Controllers;

use Config\Database;
use Config\Services;
use DateTimeZone;
use Throwable;
use ZipArchive;

class Install extends BaseController
{
    public function __construct()
    {
        // Load language helper
        helper('language');

        // Check user locale and apply language
        if (service('request')->getGet('language') && is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . service('request')->getGet('language'))) {
            // Set default language
            if (is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . service('request')->getGet('language'))) {
                session()->set('language', service('request')->getGet('language'));

                service('language')->setLocale(service('request')->getGet('language'));
            }
        } elseif (in_array(session()->get('language'), ['ar', 'de', 'en', 'en-pi', 'id', 'es', 'fr', 'id', 'ja', 'ko', 'nl', 'pt', 'ru', 'th', 'vi', 'zh'], true)) {
            service('language')->setLocale(session()->get('language'));
        } else {
            session()->set('language', 'en');
            service('language')->setLocale(session()->get('language'));
        }

        // Set timezone
        if (! session()->get('timezone') && service('request')->getPost('timezone')) {
            session()->set('timezone', service('request')->getPost('timezone'));
        } elseif (session()->get('timezone')) {
            // Set default timezone
            date_default_timezone_set(session()->get('timezone'));
        }
    }

    public function index()
    {
        return view('index');
    }

    public function requirement()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX()) {
            exit(header('Location:' . base_url()));
        }

        if (service('request')->getPost('_token')) {
            // Set validation rules
            service('validation')->setRule('agree', phrase('Agreement'), 'required');

            // Validate submitted data
            if (service('validation')->run(service('request')->getPost()) === false) {
                // Submitted data is not valid
                return $this->response->setJSON([
                    'status' => 400,
                    'validation' => service('validation')->getErrors()
                ]);
            }
        }

        // Load active extensions from server
        $extension = array_map('strtolower', get_loaded_extensions());

        // Check rewrite module status
        $modRewrite = ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules(), true)) || php_sapi_name() == 'fpm-fcgi' ? true : false);

        $output = [
            'extension' => $extension,
            'mod_rewrite' => $modRewrite
        ];

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.requirement',
            'passed' => '.none',
            'html' => view('requirement', $output)
        ]);
    }

    public function database()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX()) {
            exit(header('Location:' . base_url()));
        }

        // Database driver's options
        $output = [
            'driver' => [
                'MySQLi' => 'MySQLi',
                'SQLSRV' => 'Microsoft SQL Server',
                'Postgre' => 'PostgreSQL',
                'SQLite3' => 'SQLite',
                'OCI8' => 'Oracle (OCI8)'
            ]
        ];

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.database',
            'passed' => '.requirement',
            'html' => view('database', $output)
        ]);
    }

    public function security()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX()) {
            exit(header('Location:' . base_url()));
        }

        if (service('request')->getPost('_token')) {
            // Set validation rules
            service('validation')->setRule('database_driver', phrase('Database Driver'), 'required|in_list[MySQLi,SQLSRV,Postgre,SQLite3,OCI8]');
            service('validation')->setRule('database_hostname', phrase('Hostname'), 'required');
            service('validation')->setRule('database_port', phrase('Port'), 'required|integer');
            service('validation')->setRule('database_username', phrase('Username'), 'required');
            service('validation')->setRule('database_initial', phrase('Database Initial'), 'required');

            // Validate submitted data
            if (service('validation')->run(service('request')->getPost()) === false) {
                // Submitted data is not valid
                return $this->response->setJSON([
                    'status' => 400,
                    'validation' => service('validation')->getErrors()
                ]);
            }

            // Database config
            $_ENV['database.default.DSN'] = service('request')->getPost('database_dsn');
            $_ENV['database.default.DBDriver'] = service('request')->getPost('database_driver');
            $_ENV['database.default.hostname'] = service('request')->getPost('database_hostname');
            $_ENV['database.default.port'] = service('request')->getPost('database_port');
            $_ENV['database.default.username'] = service('request')->getPost('database_username');
            $_ENV['database.default.password'] = service('request')->getPost('database_password');
            $_ENV['database.default.charset'] = 'utf8';
            $_ENV['database.default.DBCollat'] = 'utf8_general_ci';
            $_ENV['database.default.DBDebug'] = true;

            // Create database when not available
            if (service('request')->getPost('database_forge')) {
                // Only if user allow to create database
                try {
                    // Load database forge class
                    $forge = Database::forge();

                    // Create database
                    $forge->createDatabase(service('request')->getPost('database_initial'), true);
                } catch (Throwable $e) {
                    // Connection couldn't be made, throw error
                    return $this->response->setJSON([
                        'status' => 403,
                        'message' => $e->getMessage()
                    ]);
                }
            }

            try {
                // Set database name
                $_ENV['database.default.database'] = service('request')->getPost('database_initial');

                // Connect to database
                $db = Database::connect();

                // Initialize database
                $db->initialize();
            } catch (Throwable $e) {
                // Connection couldn't be made, throw error
                return $this->response->setJSON([
                    'status' => 403,
                    'message' => $e->getMessage()
                ]);
            }

            // Store data to user's session
            session()->set([
                'database_dsn' => service('request')->getPost('database_dsn'),
                'database_driver' => service('request')->getPost('database_driver'),
                'database_hostname' => service('request')->getPost('database_hostname'),
                'database_port' => service('request')->getPost('database_port'),
                'database_username' => service('request')->getPost('database_username'),
                'database_password' => service('request')->getPost('database_password'),
                'database_initial' => service('request')->getPost('database_initial')
            ]);
        }

        $output = [
            'encryption_key' => $this->_random_string(64, true),
            'cookie_name' => $this->_random_string(16)
        ];

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.security',
            'passed' => '.database',
            'html' => view('security', $output)
        ]);
    }

    public function system()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX()) {
            exit(header('Location:' . base_url()));
        }

        if (service('request')->getPost('_token')) {
            // Set validation rules
            service('validation')->setRule('encryption', phrase('Encryption Key'), 'required|regex_match[/^[^\'\\\"]*$/]');
            service('validation')->setRule('cookie_name', phrase('Cookie Name'), 'required|regex_match[/^[a-zA-Z0-9]*$/]');
            service('validation')->setRule('session_expiration', phrase('Session Expiration'), 'required|numeric|greater_than_equal_to[0]');
            service('validation')->setRule('first_name', phrase('First Name'), 'required');
            service('validation')->setRule('email', phrase('Email'), 'required|valid_email');
            service('validation')->setRule('username', phrase('Username'), 'required|alpha_dash');
            service('validation')->setRule('password', phrase('Password'), 'required|min_length[6]');
            service('validation')->setRule('confirm_password', phrase('Password Confirmation'), 'required|min_length[6]|matches[password]');

            // Validate submitted data
            if (service('validation')->run(service('request')->getPost()) === false) {
                // Submitted data is not valid
                return $this->response->setJSON([
                    'status' => 400,
                    'validation' => service('validation')->getErrors()
                ]);
            }

            // Store data to user's session
            session()->set([
                'encryption' => 'aksaracms_' . service('request')->getPost('encryption'),
                'cookie_name' => 'aksaracms_' . service('request')->getPost('cookie_name'),
                'session_expiration' => service('request')->getPost('session_expiration'),
                'first_name' => service('request')->getPost('first_name'),
                'last_name' => service('request')->getPost('last_name'),
                'email' => service('request')->getPost('email'),
                'username' => service('request')->getPost('username'),
                'password' => service('request')->getPost('password')
            ]);
        }

        // Installation mode's options
        $output = [
            'installation_mode' => [
                [
                    'id' => 0,
                    'label' => phrase('Developer (without sample)'),
                    'selected' => false
                ], [
                    'id' => 1,
                    'label' => phrase('Basic (with sample)'),
                    'selected' => true
                ]
            ],
            'timezone' => DateTimeZone::listIdentifiers(DateTimeZone::ALL)
        ];

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.system',
            'passed' => '.security',
            'html' => view('system', $output)
        ]);
    }

    public function finalizing()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX()) {
            exit(header('Location:' . base_url()));
        }

        // Get max bytes upload filesize from server
        $maxFilesizeUnit = strtolower(preg_replace('/[^A-Za-z]+/', '', ini_get('upload_max_filesize')));

        // Set validation rules
        service('validation')->setRule('installation_mode', phrase('Installation Mode'), 'is_natural');
        service('validation')->setRule('timezone', phrase('Timezone'), 'required|timezone');
        service('validation')->setRule('site_title', phrase('Site Title'), 'required');
        service('validation')->setRule('site_description', phrase('Site Description'), 'required');
        service('validation')->setRule('file_extension', phrase('File Extension'), 'required');
        service('validation')->setRule('image_extension', phrase('Image Extension'), 'required');
        service('validation')->setRule('max_upload_size', phrase('Max Upload Size'), 'required|integer|greater_than_equal_to[1]|less_than_equal_to[' . (int) ini_get('upload_max_filesize') * ('g' == $maxFilesizeUnit ? 1024 : ('t' == $maxFilesizeUnit ? 131072 : 1)) . ']');
        service('validation')->setRule('image_dimension', phrase('Image Dimension'), 'required|integer|greater_than_equal_to[600]|less_than_equal_to[2048]');
        service('validation')->setRule('thumbnail_dimension', phrase('Thumbnail Dimension'), 'required|integer|greater_than_equal_to[250]|less_than_equal_to[600]');
        service('validation')->setRule('icon_dimension', phrase('Icon Dimension'), 'required|integer|greater_than_equal_to[80]|less_than_equal_to[250]');

        // Validate submitted data
        if (service('validation')->run(service('request')->getPost()) === false) {
            // Submitted data is not valid
            return $this->response->setJSON([
                'status' => 400,
                'validation' => service('validation')->getErrors()
            ]);
        }

        // Store data to user's session
        session()->set([
            'installation_mode' => service('request')->getPost('installation_mode'),
            'timezone' => service('request')->getPost('timezone'),
            'site_title' => service('request')->getPost('site_title'),
            'site_description' => service('request')->getPost('site_description'),
            'file_extension' => service('request')->getPost('file_extension'),
            'image_extension' => service('request')->getPost('image_extension'),
            'max_upload_size' => service('request')->getPost('max_upload_size'),
            'image_dimension' => service('request')->getPost('image_dimension'),
            'thumbnail_dimension' => service('request')->getPost('thumbnail_dimension'),
            'icon_dimension' => service('request')->getPost('icon_dimension')
        ]);

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.finalizing',
            'passed' => '.system',
            'html' => view('finalizing')
        ]);
    }

    public function run()
    {
        // Only ajax request is allowed
        if (! service('request')->isAJAX() && ! service('request')->getGet('download')) {
            exit(header('Location:' . base_url()));
        }

        if (service('request')->getPost('skip_module')) {
            // Developer mode
            session()->set('installation_mode', 0);
        }

        // Get config source
        $configSource = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'config-sample.txt');

        // Replace string in config source
        $configSource = str_replace(
            [
                '%OPENTAG%',
                '%ENCRYPTION_KEY%',
                '%COOKIE_NAME%',
                '%SESSION_EXPIRATION%',
                '%DSN%',
                '%DB_DRIVER%',
                '%DB_HOSTNAME%',
                '%DB_PORT%',
                '%DB_USERNAME%',
                '%DB_PASSWORD%',
                '%DB_DATABASE%',
                '%TIMEZONE%',
                '%DOCUMENT_EXTENSION%',
                '%IMAGE_EXTENSION%',
                '%MAX_UPLOAD_SIZE%',
                '%IMAGE_DIMENSION%',
                '%THUMBNAIL_DIMENSION%',
                '%ICON_DIMENSION%'
            ],
            [
                '<?php',
                session()->get('encryption'),
                session()->get('cookie_name'),
                (int) session()->get('session_expiration'),
                session()->get('database_dsn'),
                session()->get('database_driver'),
                session()->get('database_hostname'),
                (int) session()->get('database_port'),
                session()->get('database_username'),
                session()->get('database_password'),
                session()->get('database_initial'),
                session()->get('timezone'),
                session()->get('file_extension'),
                session()->get('image_extension'),
                (int) session()->get('max_upload_size'),
                (int) session()->get('image_dimension'),
                (int) session()->get('thumbnail_dimension'),
                (int) session()->get('icon_dimension')
            ],
            $configSource
        );

        // Validate token
        if (service('request')->getPost('_token')) {
            // Database config
            $_ENV['database.default.DSN'] = session()->get('database_dsn');
            $_ENV['database.default.DBDriver'] = session()->get('database_driver');
            $_ENV['database.default.hostname'] = session()->get('database_hostname');
            $_ENV['database.default.port'] = session()->get('database_port');
            $_ENV['database.default.username'] = session()->get('database_username');
            $_ENV['database.default.password'] = session()->get('database_password');
            $_ENV['database.default.database'] = session()->get('database_initial');
            $_ENV['database.default.charset'] = 'utf8';
            $_ENV['database.default.DBCollat'] = 'utf8_general_ci';
            $_ENV['database.default.DBDebug'] = true;

            try {
                // Initialize parameter to new connection
                $db = Database::connect();

                // Initialize database
                $db->initialize();
            } catch (Throwable $e) {
                // Unable to connect to the database
                return $this->response->setJSON([
                    'status' => 403,
                    'message' => $e->getMessage()
                ]);
            }

            // Check if basic installation is selected
            if (session()->get('installation_mode') > 0) {
                try {
                    // Try unzip the sample modules
                    $zip = new ZipArchive();

                    // Read compressed sample module
                    if ($zip->open(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'sample-module.zip') === true) {
                        // Extract sample modules to modules path
                        $zip->extractTo(ROOTPATH);
                    }

                    // Close current opened zip file
                    $zip->close();
                } catch (Throwable $e) {
                    // Unable to extract the sample module
                    return $this->response->setJSON([
                        'status' => 403,
                        'message' => phrase('Unable to extract the sample module.') . ' ' . phrase('Make sure the following directory is writable') . ': <code>' . preg_replace('/\/public/', '', ROOTPATH, 1) . 'modules</code><hr /><label class="text-danger"><input type="checkbox" name="skip_module" value="1" /> ' . phrase('Skip installing the sample module') . '</label>'
                    ]);
                }
            }

            // Check if the migration not yet executed
            if (! session()->get('migrated')) {
                // Database migrations and seeder
                try {
                    // Check if migration has been run previously
                    if ($db->tableExists(config('Migrations')->table)) {
                        // Truncate the migrations table
                        $db->table(config('Migrations')->table)->truncate();
                    }

                    // Load migration library
                    $migration = Services::migrations();

                    // Migrate the database schema (ASC order)
                    if ($migration->latest()) {
                        // Load seeder library
                        $seeder = Database::seeder();

                        // Run main seeder
                        $seeder->call('MainSeeder');

                        // Check if basic installation is selected
                        if (session()->get('installation_mode') > 0) {
                            // Run seeder to insert sample data
                            $seeder->call('DummySeeder');

                            // Run ecosystem seeder
                            if (session()->get('installation_mode') > 1) {
                                // Required by current ecosystem, suffixed with installation id
                                $seeder->call('EcosystemSeeder_' . session()->get('installation_mode'));
                            }
                        }
                    }

                    // Mark the migration has been migrated
                    session()->set('migrated', true);
                } catch (Throwable $e) {
                    // Migration couldn't be executed, throw error
                    return $this->response->setJSON([
                        'status' => 403,
                        'message' => $e->getMessage()
                    ]);
                }
            }

            // Check if configuration file is exists
            if (! file_exists(ROOTPATH . DIRECTORY_SEPARATOR . 'config.php')) {
                try {
                    // Try to writing configuration file
                    file_put_contents(ROOTPATH . DIRECTORY_SEPARATOR . 'config.php', $configSource, 1);
                } catch (Throwable $e) {
                    return $this->response->setJSON([
                        'status' => 200,
                        'active' => '.finalizing',
                        'passed' => '.system',
                        'html' => view('error')
                    ]);
                }
            }

            // Destroy session
            session()->destroy();
        } elseif (1 == service('request')->getGet('download')) {
            // Download config
            return service('response')->download('config.php', $configSource);
        }

        return $this->response->setJSON([
            'status' => 200,
            'active' => '.install',
            'passed' => '.final',
            'html' => view('finish')
        ]);
    }

    /**
     * Generate random string
     */
    private function _random_string(int $length = 32, bool $symbol = false)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($symbol) {
            // Add special symbol
            $characters .= '~`!@#%^&*()_-+|}]{[?/.,';
        }

        $charLength = strlen($characters);
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $output .= $characters[rand(0, $charLength - 1)];
        }

        return $output;
    }
}
