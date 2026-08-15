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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Modules\Administrative\Controllers\Settings;

use Aksara\Laboratory\Core;
use Aksara\Libraries\Storage;

class Settings extends Core
{
    private string $_table = 'app_settings';
    private string $_storageTable = 'app_storage';
    private array $_storageFields = [
        'provider',
        'endpoint',
        'region',
        'bucket',
        'access_key',
        'secret_key',
        'sync_existing_uploads'
    ];
    private array $_storageProviders = [
        'disabled' => 'None / Disabled',
        's3' => 'Amazon S3',
        'r2' => 'Cloudflare R2',
        'spaces' => 'DigitalOcean Spaces',
        'minio' => 'MinIO',
        'wasabi' => 'Wasabi'
    ];
    private array $_storageData = [];

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->searchable(false);

        $this->setMethod('update');
        $this->setUploadPath('settings');

        // Use vertical schema (EAV table structure)
        $this->verticalSchema('key', 'value', 'type');
    }

    public function index()
    {
        if ('storageProvider' === $this->request->getPost('fetch')) {
            return $this->_fetchStorageProvider();
        }

        $defaultMapTile = null;
        $requiredApiKey = null;
        $requiredAnalyticKey = null;
        $requiredFacebookAppId = null;
        $requiredFacebookAppSecret = null;
        $requiredGoogleClientId = null;
        $requiredGoogleClientSecret = null;
        $requiredStorageEndpoint = null;

        if ($this->request->getPost('provider') && 'disabled' !== $this->request->getPost('provider')) {
            $requiredStorageEndpoint = 'required|';
        }

        if ($this->request->getPost('openlayers_search_provider') && in_array($this->request->getPost('openlayers_search_provider'), ['google', 'osm'])) {
            $requiredApiKey = 'required|';
        }

        if ($this->request->getPost('default_map_tile')) {
            $defaultMapTile = 'valid_url';
        }

        if ($this->request->getPost('google_analytics_key')) {
            $requiredAnalyticKey = 'required|';
        }

        if ($this->request->getPost('facebook_app_id')) {
            $requiredFacebookAppSecret = 'required';
        } elseif ($this->request->getPost('facebook_app_secret')) {
            $requiredFacebookAppId = 'required';
        }

        if ($this->request->getPost('google_client_id')) {
            $requiredGoogleClientSecret = 'required';
        } elseif ($this->request->getPost('google_client_secret')) {
            $requiredGoogleClientId = 'required';
        }

        $storage = $this->_storage();

        $this->setTitle(phrase('Application Settings'))
        ->setIcon('mdi mdi-wrench-outline')
        ->addField([
            'provider' => 'varchar',
            'endpoint' => 'varchar',
            'region' => 'varchar',
            'bucket' => 'varchar',
            'access_key' => 'varchar',
            'secret_key' => 'varchar',
            'sync_existing_uploads' => 'boolean'
        ])
        ->setDefault($storage)
        ->defaultValue($storage)
        ->setField([
            'app_description' => 'textarea',
            'app_logo' => 'image',
            'app_icon' => 'image',
            'reports_icon' => 'image',
            'office_address' => 'textarea',
            'office_map' => 'geospatial',
            'force_system_language' => 'boolean',
            'one_device_login' => 'boolean',
            'login_attempt' => 'number_format',
            'blocking_time' => 'number_format',
            'account_age_restriction' => 'number_format',
            'spam_timer' => 'number_format',
            'username_change' => 'boolean',
            'frontend_registration' => 'boolean',
            'auto_active_registration' => 'boolean',
            'facebook_app_secret' => 'encryption',
            'google_client_secret' => 'encryption',
            'action_sound' => 'boolean',
            'update_check' => 'boolean',
            'smtp_port' => 'integer',
            'smtp_password' => 'encryption',
            'ai_enabled' => 'boolean',
            'ai_image_enabled' => 'boolean',
            'ai_api_key' => 'encryption',
            'ai_temperature' => 'number_format',
            'ai_max_tokens' => 'number_format',
            'access_key' => 'encryption',
            'secret_key' => 'encryption',
            'sync_existing_uploads' => 'boolean'
        ])
        ->setField(
            'ai_provider',
            'select',
            [
                'openai' => 'OpenAI',
                'anthropic' => 'Anthropic / Claude',
                'gemini' => 'Google Gemini',
                'deepseek' => 'DeepSeek',
                'openrouter' => 'OpenRouter',
                'openai_compatible' => phrase('OpenAI Compatible'),
                'custom' => phrase('Custom Provider')
            ]
        )
        ->setField(
            'provider',
            'select',
            $this->_storageProviders
        )
        ->setField(
            'openlayers_search_provider',
            'radio',
            [
                'openlayers' => 'OpenLayers',
                'google' => 'Google',
                'osm' => 'OpenStreetMap'
            ]
        )
        ->fieldAppend([
            'login_attempt' => phrase('times'),
            'blocking_time' => phrase('minutes'),
            'account_age_restriction' => phrase('days'),
            'spam_timer' => phrase('seconds')
        ])
        ->setAttribute([
            'office_map' => 'data-drawing-type="coordinate" data-draggable="1" data-zoom="12"',
            'provider' => 'data-storage-provider="1" onchange="aksaraStorageProviderChanged(this)"'
        ])
        ->setPlaceholder([
            'openlayers_search_key' => phrase('Enter your API Key'),
            'default_map_tile' => 'E.g: https://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
            'ai_api_key' => phrase('Enter your API Key'),
            'ai_model' => 'gpt-5.6',
            'ai_image_model' => 'gpt-image-2',
            'ai_base_url' => phrase('Optional custom base URL'),
            'ai_temperature' => '0.7',
            'ai_max_tokens' => '2048',
            'endpoint' => 'https://s3.amazonaws.com',
            'region' => 'us-east-1',
            'bucket' => phrase('Bucket name')
        ])
        ->setTooltip([
            'login_attempt' => phrase('Maximum number of login attempts.'),
            'blocking_time' => phrase('Blocking time when reaching maximum login attempts.'),
            'account_age_restriction' => phrase('How many days before user can post interaction after registration.'),
            'spam_timer' => phrase('How many seconds before user can post another comment.'),
            'openlayers_search_key' => phrase('The API Key is required when you using Google as search provider.'),
            'default_map_tile' => phrase('You can use any XYZ Tile Source as a default map tiles.'),
            'ai_enabled' => phrase('Enable AI features for content assistance.'),
            'ai_image_enabled' => phrase('Allow AI to generate images for image upload fields.'),
            'ai_provider' => phrase('Choose the AI provider used by content tools.'),
            'ai_api_key' => phrase('Stored encrypted and used by server-side AI requests.'),
            'ai_model' => phrase('Default model used for AI content generation and translation.'),
            'ai_image_model' => phrase('Model used for generating images for image upload fields.'),
            'ai_base_url' => phrase('Optional API base URL for OpenAI-compatible providers.'),
            'ai_temperature' => phrase('Controls output creativity. Lower values are more deterministic.'),
            'ai_max_tokens' => phrase('Maximum generated tokens returned by default.'),
            'provider' => phrase('The selected provider will become active after saving.'),
            'endpoint' => phrase('Required for S3-compatible storage such as MinIO, Cloudflare R2, DigitalOcean Spaces, or Wasabi.'),
            'sync_existing_uploads' => phrase('Sync existing uploads after saving. When enabling cloud storage, local files are uploaded to cloud. When disabling cloud storage, cloud files are downloaded to local storage.')
        ])
        ->fieldAppend([
            'default_map_tile' => '<a href="https://wiki.openstreetmap.org/wiki/Raster_tile_providers" target="_blank">Reference<i class="mdi mdi-launch"></i></a>'
        ])
        ->setRelation(
            'app_language',
            'app_languages.id',
            '{{ app_languages.language }}',
            [
                'app_languages.status' => 1
            ]
        )
        ->setRelation(
            'default_membership_group',
            'app_groups.group_id',
            '{{ app_groups.group_name }}',
            [
                'app_groups.group_id > ' => 2
            ]
        )
        ->setValidation([
            'app_name' => 'required|string|max_length[64]',
            'app_description' => 'string|max_length[255]',
            'app_language' => 'required',
            'force_system_language' => 'boolean',
            'office_name' => 'required|max_length[255]',
            'office_email' => 'required|valid_email|max_length[255]',
            'office_phone' => 'required|max_length[16]',
            'office_address' => 'required|max_length[255]',
            'office_map' => 'required|valid_geojson',

            /* MEMBERSHIP */
            'username_change' => 'boolean',
            'frontend_registration' => 'boolean',
            'auto_active_registration' => 'boolean',
            'one_device_login' => 'boolean',
            'login_attempt' => 'numeric|max_length[5]',
            'blocking_time' => 'numeric|max_length[5]',
            'account_age_restriction' => 'numeric|max_length[3]',
            'spam_timer' => 'numeric|max_length[5]',

            /* STORAGE */
            'provider' => 'in_list[disabled,s3,r2,spaces,minio,wasabi]',
            'endpoint' => ($requiredStorageEndpoint ? $requiredStorageEndpoint . 'valid_url|max_length[255]' : 'permit_empty|valid_url|max_length[255]'),
            'sync_existing_uploads' => 'boolean',

            /* APIS */
            'openlayers_search_provider' => 'in_list[openlayers,google,osm]',
            'openlayers_search_key' => ($requiredApiKey ? $requiredApiKey . 'alpha_dash|max_length[128]' : null),
            'maps_provider' => 'in_list[disabled,google,openlayers]',
            'default_map_tile' => $defaultMapTile,
            'google_analytics_key' => ($requiredAnalyticKey ? $requiredAnalyticKey . 'alpha_dash|max_length[32]' : null),

            /* AI */
            'ai_enabled' => 'boolean',
            'ai_image_enabled' => 'boolean',
            'ai_provider' => 'in_list[openai,anthropic,gemini,deepseek,openrouter,openai_compatible,custom]',
            'ai_api_key' => 'permit_empty|max_length[512]',
            'ai_model' => 'permit_empty|string|max_length[128]',
            'ai_image_model' => 'permit_empty|string|max_length[128]',
            'ai_base_url' => 'permit_empty|valid_url|max_length[255]',
            'ai_temperature' => 'permit_empty|decimal',
            'ai_max_tokens' => 'permit_empty|integer',

            /* OAUTH */
            'facebook_app_id' => $requiredFacebookAppId,
            'facebook_app_secret' => $requiredFacebookAppSecret,
            'google_client_id' => $requiredGoogleClientId,
            'google_client_secret' => $requiredGoogleClientSecret,

            /* NOTIFIER */
            'action_sound' => 'boolean',
            'update_check' => 'boolean',
            'smtp_port' => 'integer|max_length[5]'
        ])
        ->setAlias([
            'app_name' => phrase('Application Name'),
            'app_description' => phrase('Application Description'),
            'office_name' => phrase('Office Name'),
            'office_email' => phrase('Office Email'),
            'office_phone' => phrase('Office Phone'),
            'office_fax' => phrase('Office Fax'),
            'whatsapp_number' => phrase('WhatsApp Number'),
            'instagram_username' => phrase('Instagram Username'),
            'twitter_username' => phrase('Twitter Username'),
            'office_address' => phrase('Office Address'),
            'office_map' => phrase('Office Map'),
            'app_logo' => phrase('Application Logo'),
            'app_icon' => phrase('Application Icon'),
            'reports_icon' => phrase('Reports Icon'),
            'app_language' => phrase('System Language'),
            'force_system_language' => phrase('Force System Language'),

            /* MEMBERSHIP */
            'one_device_login' => phrase('One Device Login'),
            'login_attempt' => phrase('Login Attempt'),
            'blocking_time' => phrase('Blocking Time'),
            'account_age_restriction' => phrase('Day Restriction'),
            'spam_timer' => phrase('Spam Timer'),
            'frontend_registration' => phrase('Enable Public Registration'),
            'default_membership_group' => phrase('Default Membership Group'),
            'auto_active_registration' => phrase('Auto Active Registration'),
            'username_change' => phrase('Enable Username Changes'),

            /* APIS */
            'maps_provider' => phrase('Maps Provider'),
            'openlayers_search_provider' => phrase('OpenLayers Search Provider'),
            'openlayers_search_key' => phrase('OpenLayers Search Key'),
            'default_map_tile' => phrase('Default Map Tile'),
            'google_analytics_key' => phrase('Google Analytics Key'),

            /* AI */
            'ai_enabled' => phrase('Enable AI'),
            'ai_image_enabled' => phrase('Generate Images'),
            'ai_provider' => phrase('AI Provider'),
            'ai_api_key' => phrase('AI API Key'),
            'ai_model' => phrase('AI Model'),
            'ai_image_model' => phrase('AI Image Model'),
            'ai_base_url' => phrase('AI Base URL'),
            'ai_temperature' => phrase('Temperature'),
            'ai_max_tokens' => phrase('Max Tokens'),

            /* OAUTH */
            'facebook_app_id' => phrase('Facebook APP ID'),
            'facebook_app_secret' => phrase('Facebook APP Secret'),
            'google_client_id' => phrase('Google Client ID'),
            'google_client_secret' => phrase('Google Client Secret'),

            /* NOTIFIER */
            'action_sound' => phrase('Action Sound'),
            'update_check' => phrase('Update Check'),
            'smtp_hostname' => phrase('SMTP Hostname'),
            'smtp_port' => phrase('SMTP Port'),
            'smtp_username' => phrase('SMTP Username'),
            'smtp_password' => phrase('SMTP Password'),

            /* STORAGE */
            'provider' => phrase('Provider'),
            'endpoint' => phrase('Endpoint'),
            'region' => phrase('Region'),
            'bucket' => phrase('Bucket'),
            'access_key' => phrase('Access Key'),
            'secret_key' => phrase('Secret Key'),
            'sync_existing_uploads' => phrase('Sync Existing Uploads')
        ])

        ->render($this->_table);
    }

    public function beforeUpdate()
    {
        $post = $this->request->getPost();

        if (! $post) {
            return;
        }

        foreach ($post as $key => $value) {
            if (in_array($key, $this->_storageFields)) {
                $this->_storageData[$key] = $value;

                unset($post[$key]);
            }
        }

        $this->request->setGlobal('post', $post);
    }

    public function afterUpdate()
    {
        service('cache')->delete('aksara_app_settings');

        if (! $this->_storageData || ! $this->model->tableExists($this->_storageTable)) {
            return;
        }

        $provider = $this->_storageData['provider'] ?? null;

        if (! isset($this->_storageProviders[$provider])) {
            return;
        }

        if ('disabled' === $provider) {
            $activeStorage = $this->model->getWhere(
                $this->_storageTable,
                [
                    'status' => 1
                ],
                1
            )
            ->row();

            if (! empty($this->_storageData['sync_existing_uploads'])) {
                $this->_extendStorageSyncExecutionTime();
                $this->_syncExistingUploadsFromCloud($activeStorage);
            }

            $this->model->update($this->_storageTable, ['status' => 0]);
            $this->model->delete($this->_storageTable, ['provider' => 'disabled']);
            $this->_refreshStorageCache();

            return;
        }

        $storage = $this->model->getWhere(
            $this->_storageTable,
            [
                'provider' => $provider
            ],
            1
        )
        ->row();
        $encrypter = service('encrypter');
        $accessKey = trim($this->_storageData['access_key'] ?? '');
        $secretKey = trim($this->_storageData['secret_key'] ?? '');

        $data = [
            'provider' => $provider,
            'endpoint' => trim($this->_storageData['endpoint'] ?? ''),
            'region' => trim($this->_storageData['region'] ?? ''),
            'bucket' => trim($this->_storageData['bucket'] ?? ''),
            'access_key' => ('' === $accessKey || '*****' === $accessKey ? ($storage->access_key ?? '') : base64_encode($encrypter->encrypt($accessKey))),
            'secret_key' => ('' === $secretKey || '*****' === $secretKey ? ($storage->secret_key ?? '') : base64_encode($encrypter->encrypt($secretKey))),
            'status' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => get_userdata('user_id') ?: 1
        ];

        if ($this->model->fieldExists('name', $this->_storageTable)) {
            $data['name'] = $storage->name ?? ($this->_storageProviders[$provider] ?? 'A');
        }

        $this->model->update($this->_storageTable, ['status' => 0]);

        if ($storage) {
            $this->model->update($this->_storageTable, $data, ['provider' => $provider]);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = get_userdata('user_id') ?: 1;

            $this->model->insert($this->_storageTable, $data);
        }

        $this->_refreshStorageCache();

        if (! empty($this->_storageData['sync_existing_uploads'])) {
            $this->_extendStorageSyncExecutionTime();
            $storage = $this->model->getWhere($this->_storageTable, ['provider' => $provider])->row();
            $this->_syncExistingUploads($storage);
        }
    }

    private function _fetchStorageProvider()
    {
        $provider = $this->request->getPost('provider');

        if (! isset($this->_storageProviders[$provider])) {
            return make_json([
                'status' => 400,
                'message' => phrase('The selected provider is not valid.')
            ]);
        }

        $storage = $this->_storage($provider);

        return make_json([
            'status' => 200,
            'results' => $storage
        ]);
    }

    private function _refreshStorageCache(): void
    {
        if (! function_exists('get_active_storage')) {
            helper('file');
        }

        get_active_storage(true);
    }

    private function _extendStorageSyncExecutionTime(): void
    {
        if (function_exists('ini_set')) {
            ini_set('max_execution_time', '0');
        }

        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        }
    }

    private function _syncExistingUploads(?object $config = null): void
    {
        if (! $config) {
            return;
        }

        $uploadPath = defined('FCPATH') ? FCPATH . UPLOAD_PATH : ROOTPATH . 'public' . DIRECTORY_SEPARATOR . UPLOAD_PATH;
        $uploadPath = rtrim(str_replace('\\', '/', $uploadPath), '/');

        if (! is_dir($uploadPath)) {
            log_message('warning', 'Upload sync skipped because local upload path does not exist: ' . $uploadPath);

            return;
        }

        $storage = new Storage($config);
        $directory = new \RecursiveDirectoryIterator($uploadPath, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $local = str_replace('\\', '/', $file->getPathname());
            $remote = ltrim(substr($local, strlen($uploadPath)), '/');

            if (! $remote || $this->_isIgnoredUploadPath($remote)) {
                continue;
            }

            try {
                $stream = fopen($local, 'rb');

                if (! $stream) {
                    continue;
                }

                $storage->putStream($remote, $stream);
                fclose($stream);
            } catch (\Throwable $e) {
                if (isset($stream) && is_resource($stream)) {
                    fclose($stream);
                }

                log_message('error', 'Unable to sync local upload "' . $remote . '" to cloud storage: ' . $e->getMessage());
            }
        }
    }

    private function _syncExistingUploadsFromCloud(?object $config = null): void
    {
        if (! $config || 'disabled' === strtolower((string) ($config->provider ?? ''))) {
            return;
        }

        $uploadPath = defined('FCPATH') ? FCPATH . UPLOAD_PATH : ROOTPATH . 'public' . DIRECTORY_SEPARATOR . UPLOAD_PATH;
        $uploadPath = rtrim(str_replace('\\', '/', $uploadPath), '/');

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $storage = new Storage($config);

        try {
            $contents = $storage->listContents('', true);
        } catch (\Throwable $e) {
            log_message('error', 'Unable to list cloud storage contents for local sync: ' . $e->getMessage());

            return;
        }

        foreach ($contents as $item) {
            if (! method_exists($item, 'isFile') || ! $item->isFile()) {
                continue;
            }

            $remote = trim($item->path(), '/');

            if ($this->_isIgnoredUploadPath($remote)) {
                continue;
            }

            $local = $uploadPath . '/' . $remote;
            $directory = dirname($local);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            try {
                $stream = $storage->readStream($remote);
                $target = fopen($local, 'wb');

                if (! $stream || ! $target) {
                    throw new \RuntimeException('Unable to open stream for "' . $remote . '".');
                }

                stream_copy_to_stream($stream, $target);
                fclose($stream);
                fclose($target);
            } catch (\Throwable $e) {
                if (isset($stream) && is_resource($stream)) {
                    fclose($stream);
                }

                if (isset($target) && is_resource($target)) {
                    fclose($target);
                }

                log_message('error', 'Unable to sync cloud upload "' . $remote . '" to local storage: ' . $e->getMessage());
            }
        }
    }

    private function _isIgnoredUploadPath(string $path): bool
    {
        $segments = array_filter(explode('/', trim(str_replace('\\', '/', $path), '/')), 'strlen');
        $filename = strtolower((string) end($segments));
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $ignoredNames = [
            '.ds_store',
            'thumbs.db',
            'desktop.ini',
            '.htaccess',
            '.htpasswd',
            'index.html',
            'index.htm'
        ];
        $ignoredDirectories = [
            '.spotlight-v100',
            '.trashes',
            '__macosx'
        ];

        if (! $filename || in_array($filename, $ignoredNames) || in_array($extension, ['html', 'htm'])) {
            return true;
        }

        if (str_starts_with($filename, '._')) {
            return true;
        }

        foreach ($segments as $segment) {
            if (in_array(strtolower($segment), $ignoredDirectories)) {
                return true;
            }
        }

        return false;
    }

    private function _storage(?string $provider = null): array
    {
        $activeStorage = null;

        if ($this->model->tableExists($this->_storageTable)) {
            $activeStorage = $this->model->getWhere($this->_storageTable, ['status' => 1], 1)->rowArray();

            if (! $provider && $activeStorage) {
                $provider = $activeStorage['provider'] ?? 'disabled';
            }
        }

        if (! $provider || ! isset($this->_storageProviders[$provider])) {
            $provider = 'disabled';
        }

        $default = $this->_storageDefaults($provider);

        if ('disabled' === $provider || ! $this->model->tableExists($this->_storageTable)) {
            return $default;
        }

        $storage = ($activeStorage && ($activeStorage['provider'] ?? null) == $provider)
            ? $activeStorage
            : ($this->model->getWhere($this->_storageTable, ['provider' => $provider], 1)->rowArray() ?? []);

        unset($storage['access_key'], $storage['secret_key']);

        return array_merge($default, $storage);
    }

    private function _storageDefaults(string $provider = 'disabled'): array
    {
        $defaults = [
            'disabled' => [
                'endpoint' => null,
                'region' => null
            ],
            's3' => [
                'endpoint' => 'https://s3.amazonaws.com',
                'region' => 'us-east-1'
            ],
            'r2' => [
                'endpoint' => 'https://<account_id>.r2.cloudflarestorage.com',
                'region' => 'auto'
            ],
            'spaces' => [
                'endpoint' => 'https://<region>.digitaloceanspaces.com',
                'region' => 'sgp1'
            ],
            'minio' => [
                'endpoint' => 'http://localhost:9000',
                'region' => 'us-east-1'
            ],
            'wasabi' => [
                'endpoint' => 'https://s3.wasabisys.com',
                'region' => 'us-east-1'
            ]
        ];

        return array_merge([
            'provider' => $provider,
            'endpoint' => null,
            'region' => null,
            'bucket' => null,
            'access_key' => null,
            'secret_key' => null,
            'sync_existing_uploads' => 0
        ], $defaults[$provider] ?? $defaults['disabled']);
    }

    private function _decryptStorageSecret(?string $value = null): string
    {
        if (! $value) {
            return '';
        }

        try {
            return service('encrypter')->decrypt(base64_decode($value, true));
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
