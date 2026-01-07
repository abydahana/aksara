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

namespace Aksara\Modules\Administrative\Controllers\Settings;

use Aksara\Laboratory\Core;

class Settings extends Core
{
    private $_table = 'app__settings';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->searchable(false);

        $this->setMethod('update');
        $this->setUploadPath('settings');
    }

    public function index()
    {
        $defaultMapTile = null;
        $requiredApiKey = null;
        $requiredAnalyticKey = null;
        $requiredFacebookAppId = null;
        $requiredFacebookAppSecret = null;
        $requiredGoogleClientId = null;
        $requiredGoogleClientSecret = null;

        if ($this->request->getPost('openlayers_search_provider') && in_array($this->request->getPost('openlayers_search_provider'), ['google', 'osm'], true)) {
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

        $this->setTitle(phrase('Application Settings'))
        ->setIcon('mdi mdi-wrench-outline')
        ->setPrimary('id')
        ->unsetField('id')
        ->setField([
            'app_description' => 'textarea',
            'app_logo' => 'image',
            'app_icon' => 'image',
            'reports_logo' => 'image',
            'office_address' => 'textarea',
            'office_map' => 'geospatial',
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
            'smtp_password' => 'encryption'
        ])
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
            'office_map' => 'data-drawing-type="coordinate" data-draggable="1"'
        ])
        ->setPlaceholder([
            'openlayers_search_key' => phrase('Enter your API Key'),
            'default_map_tile' => 'E.g: https://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}'
        ])
        ->setTooltip([
            'login_attempt' => phrase('Maximum number of login attempts.'),
            'blocking_time' => phrase('Blocking time when reaching maximum login attempts.'),
            'account_age_restriction' => phrase('How many days before user can post interaction after registration.'),
            'spam_timer' => phrase('How many seconds before user can post another comment.'),
            'openlayers_search_key' => phrase('The API Key is required when you using Google as search provider.'),
            'default_map_tile' => phrase('You can use any XYZ Tile Source as a default map tiles.')
        ])
        ->fieldAppend([
            'default_map_tile' => '<a href="https://wiki.openstreetmap.org/wiki/Raster_tile_providers" target="_blank">Reference<i class="mdi mdi-launch"></i></a>'
        ])
        ->setRelation(
            'app_language',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->setRelation(
            'default_membership_group',
            'app__groups.group_id',
            '{{ app__groups.group_name }}',
            [
                'app__groups.group_id > ' => 2
            ]
        )
        ->setValidation([
            'app_name' => 'required|string|max_length[60]',
            'app_description' => 'string',
            'app_language' => 'required',
            'office_name' => 'required',
            'office_email' => 'required|valid_email',
            'office_phone' => 'required',
            'office_address' => 'required',

            /* MEMBERSHIP */
            'username_change' => 'boolean',
            'frontend_registration' => 'boolean',
            'auto_active_registration' => 'boolean',
            'one_device_login' => 'boolean',
            'login_attempt' => 'numeric|max_length[5]',
            'blocking_time' => 'numeric|max_length[5]',
            'account_age_restriction' => 'numeric|max_length[3]',
            'spam_timer' => 'numeric|max_length[5]',

            /* APIS */
            'openlayers_search_provider' => 'in_list[openlayers,google,osm]',
            'openlayers_search_key' => ($requiredApiKey ? $requiredApiKey . 'alpha_dash|max_length[128]' : null),
            'maps_provider' => 'in_list[disabled,google,openlayers]',
            'default_map_tile' => $defaultMapTile,
            'google_analytics_key' => ($requiredAnalyticKey ? $requiredAnalyticKey . 'alpha_dash|max_length[32]' : null),

            /* OAUTH */
            'facebook_app_id' => $requiredFacebookAppId,
            'facebook_app_secret' => $requiredFacebookAppSecret,
            'google_client_id' => $requiredGoogleClientId,
            'google_client_secret' => $requiredGoogleClientSecret,

            /* NOTIFIER */
            'action_sound' => 'boolean',
            'update_check' => 'boolean',
            'smtp_port' => 'integer'
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
            'app_language' => phrase('System Language'),

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

            /* OATH */
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
            'smtp_password' => phrase('SMTP Password')
        ])
        ->where([
            'id' => 1
        ])
        ->limit(1)

        ->render($this->_table);
    }
}
