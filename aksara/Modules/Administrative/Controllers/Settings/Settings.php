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

class Settings extends \Aksara\Laboratory\Core
{
    private $_table = 'app__settings';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->searchable(false);

        $this->set_method('update');
        $this->set_upload_path('settings');
    }

    public function index()
    {
        $default_map_tile = null;
        $required_api_key = null;
        $required_analytic_key = null;
        $required_facebook_app_id = null;
        $required_facebook_app_secret = null;
        $required_google_client_id = null;
        $required_google_client_secret = null;
        $required_email_masking = null;
        $required_smtp_host = null;

        if (service('request')->getPost('openlayers_search_provider') && in_array(service('request')->getPost('openlayers_search_provider'), ['google', 'osm'])) {
            $required_api_key = 'required|';
        }

        if (service('request')->getPost('default_map_tile')) {
            $default_map_tile = 'valid_url';
        }

        if (service('request')->getPost('google_analytics_key')) {
            $required_analytic_key = 'required|';
        }

        if (service('request')->getPost('facebook_app_id')) {
            $required_facebook_app_secret = 'required';
        } elseif (service('request')->getPost('facebook_app_secret')) {
            $required_facebook_app_id = 'required';
        }

        if (service('request')->getPost('google_client_id')) {
            $required_google_client_secret = 'required';
        } elseif (service('request')->getPost('google_client_secret')) {
            $required_google_client_id = 'required';
        }

        if (service('request')->getPost('smtp_email_masking')) {
            $required_email_masking = 'valid_email';
        }

        $this->set_title(phrase('Application Settings'))
        ->set_icon('mdi mdi-wrench-outline')
        ->set_primary('id')
        ->unset_field('id')
        ->set_field([
            'app_description' => 'textarea',
            'app_logo' => 'image',
            'app_icon' => 'image',
            'reports_logo' => 'image',
            'office_address' => 'textarea',
            'office_map' => 'geospatial',
            'one_device_login' => 'boolean',
            'login_attempt' => 'number_format',
            'blocking_time' => 'number_format',
            'username_change' => 'boolean',
            'frontend_registration' => 'boolean',
            'auto_active_registration' => 'boolean',
            'facebook_app_secret' => 'encryption',
            'google_client_secret' => 'encryption',
            'action_sound' => 'boolean',
            'update_check' => 'boolean',
            'smtp_password' => 'encryption'
        ])
        ->set_field(
            'openlayers_search_provider',
            'radio',
            [
                'openlayers' => 'OpenLayers',
                'google' => 'Google',
                'osm' => 'OpenStreetMap'
            ]
        )
        ->field_append([
            'login_attempt' => phrase('times'),
            'blocking_time' => phrase('minutes')
        ])
        ->set_attribute([
            'office_map' => 'data-drawing-type="coordinate" data-draggable="1"'
        ])
        ->set_placeholder([
            'openlayers_search_key' => phrase('Enter your API Key'),
            'default_map_tile' => 'E.g: https://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}'
        ])
        ->set_tooltip([
            'openlayers_search_key' => phrase('The API Key is required when you using Google as search provider'),
            'default_map_tile' => phrase('You can use any XYZ Tile Source as a default map tiles')
        ])
        ->set_relation(
            'app_language',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->set_relation(
            'default_membership_group',
            'app__groups.group_id',
            '{{ app__groups.group_name }}',
            [
                'app__groups.group_id > ' => 2
            ]
        )
        ->set_validation([
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

            /* APIS */
            'openlayers_search_provider' => 'in_list[openlayers,google,osm]',
            'openlayers_search_key' => ($required_api_key ? $required_api_key . 'alpha_dash|max_length[128]' : null),
            'maps_provider' => 'in_list[disabled,google,openlayers]',
            'default_map_tile' => $default_map_tile,
            'google_analytics_key' => ($required_analytic_key ? $required_analytic_key . 'alpha_dash|max_length[32]' : null),
            'disqus_site_domain' => (service('request')->getPost('disqus_site_domain') ? 'valid_url|max_length[128]' : null),

            /* OAUTH */
            'facebook_app_id' => $required_facebook_app_id,
            'facebook_app_secret' => $required_facebook_app_secret,
            'google_client_id' => $required_google_client_id,
            'google_client_secret' => $required_google_client_secret,

            /* NOTIFIER */
            'action_sound' => 'boolean',
            'update_check' => 'boolean',
            'smtp_email_masking' => $required_email_masking,
            'smtp_port' => 'numeric|max_length[5]'
        ])
        ->set_alias([
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
            'disqus_site_domain' => phrase('Disqus Site Domain'),

            /* OATH */
            'facebook_app_id' => phrase('Facebook APP ID'),
            'facebook_app_secret' => phrase('Facebook APP Secret'),
            'google_client_id' => phrase('Google Client ID'),
            'google_client_secret' => phrase('Google Client Secret'),

            /* NOTIFIER */
            'action_sound' => phrase('Action Sound'),
            'update_check' => phrase('Update Check'),
            'smtp_email_masking' => phrase('SMTP Email Masking'),
            'smtp_sender_masking' => phrase('SMTP Sender Masking'),
            'smtp_host' => phrase('SMTP Host'),
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
