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

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Insert main language (default)
        $this->db->table('app_languages')->insertBatch([
            [
                'language' => 'Default (English)',
                'code' => 'en',
                'locale' => 'en-US,en_US,en_US.UTF8,en-us,en,english',
                'status' => 1
            ],
            [
                'language' => 'English (Pirate)',
                'code' => 'en-pir',
                'locale' => 'en-x-pirate,en_pirate,en-pir,pirate',
                'status' => 1
            ],
            [
                'language' => 'Bahasa Indonesia',
                'code' => 'id',
                'locale' => 'id-ID,id_ID,id_ID.UTF8,id-id,id,indonesian',
                'status' => 1
            ],
            [
                'language' => 'Español',
                'code' => 'es',
                'locale' => 'es-ES,es_ES,es_ES.UTF8,es-es,es,spanish',
                'status' => 1
            ],
            [
                'language' => 'Deutsch',
                'code' => 'de',
                'locale' => 'de-DE,de_DE,de_DE.UTF8,de-de,de,german',
                'status' => 1
            ],
            [
                'language' => 'Français',
                'code' => 'fr',
                'locale' => 'fr-FR,fr_FR,fr_FR.UTF8,fr-fr,fr,french',
                'status' => 1
            ],
            [
                'language' => 'Nederlands',
                'code' => 'nl',
                'locale' => 'nl-NL,nl_NL,nl_NL.UTF8,nl-nl,nl,dutch',
                'status' => 1
            ],
            [
                'language' => 'Português',
                'code' => 'pt',
                'locale' => 'pt-PT,pt_PT,pt_PT.UTF8,pt-pt,pt,portuguese',
                'status' => 1
            ],
            [
                'language' => 'Русский',
                'code' => 'ru',
                'locale' => 'ru-RU,ru_RU,ru_RU.UTF8,ru-ru,ru,russian',
                'status' => 1
            ],
            [
                'language' => '简体中文',
                'code' => 'zh',
                'locale' => 'zh-CN,zh_CN,zh_CN.UTF8,zh-cn,zh,chinese',
                'status' => 1
            ],
            [
                'language' => 'العربية',
                'code' => 'ar',
                'locale' => 'ar-SA,ar_SA,ar_SA.UTF8,ar-sa,ar,arabic',
                'status' => 1
            ],
            [
                'language' => '日本語',
                'code' => 'ja',
                'locale' => 'ja-JP,ja_JP,ja_JP.UTF8,ja-jp,ja,japanese',
                'status' => 1
            ],
            [
                'language' => '한국어',
                'code' => 'ko',
                'locale' => 'ko-KR,ko_KR,ko_KR.UTF8,ko-kr,ko,korean',
                'status' => 1
            ],
            [
                'language' => 'ไทย',
                'code' => 'th',
                'locale' => 'th-TH,th_TH,th_TH.UTF8,th-th,th,thai',
                'status' => 1
            ],
            [
                'language' => 'Tiếng Việt',
                'code' => 'vi',
                'locale' => 'vi-VN,vi_VN,vi_VN.UTF8,vi-vn,vi,vietnamese',
                'status' => 1
            ],
        ]);

        // Insert the main configuration to app_settings (Vertical Schema)
        $settings_data = [
            [
                'key' => 'app_name',
                'type' => 'varchar',
                'value' => htmlspecialchars(trim(session()->get('site_title') ?? ''))
            ],
            [
                'key' => 'app_description',
                'type' => 'text',
                'value' => htmlspecialchars(trim(session()->get('site_description') ?? ''))
            ],
            [
                'key' => 'app_logo',
                'type' => 'image',
                'value' => 'logo.png'
            ],
            [
                'key' => 'app_icon',
                'type' => 'image',
                'value' => 'logo.png'
            ],
            [
                'key' => 'frontend_theme',
                'type' => 'varchar',
                'value' => 'default'
            ],
            [
                'key' => 'backend_theme',
                'type' => 'varchar',
                'value' => 'backend'
            ],
            [
                'key' => 'app_language',
                'type' => 'select',
                'value' => htmlspecialchars(trim(session()->get('language') == 'id' ? '2' : '1'))
            ],
            [
                'key' => 'office_name',
                'type' => 'varchar',
                'value' => 'Aksara Laboratory'
            ],
            [
                'key' => 'office_phone',
                'type' => 'varchar',
                'value' => '+6281381614558'
            ],
            [
                'key' => 'office_email',
                'type' => 'varchar',
                'value' => 'info@example.com'
            ],
            [
                'key' => 'office_fax',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'office_address',
                'type' => 'text',
                'value' => '2nd Floor Example Tower Building, Some Road Name, Any Region'
            ],
            [
                'key' => 'office_map',
                'type' => 'text',
                'value' => ''
            ],
            [
                'key' => 'one_device_login',
                'type' => 'boolean',
                'value' => '0'
            ],
            [
                'key' => 'username_change',
                'type' => 'boolean',
                'value' => '1'
            ],
            [
                'key' => 'frontend_registration',
                'type' => 'boolean',
                'value' => '1'
            ],
            [
                'key' => 'default_membership_group',
                'type' => 'select',
                'value' => '3'
            ],
            [
                'key' => 'auto_active_registration',
                'type' => 'boolean',
                'value' => '1'
            ],
            [
                'key' => 'google_analytics_key',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'openlayers_search_provider',
                'type' => 'varchar',
                'value' => 'openlayers'
            ],
            [
                'key' => 'openlayers_search_key',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'default_map_tile',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'facebook_app_id',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'facebook_app_secret',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'google_client_id',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'google_client_secret',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'twitter_username',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'instagram_username',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'whatsapp_number',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'action_sound',
                'type' => 'boolean',
                'value' => '1'
            ],
            [
                'key' => 'login_attempt',
                'type' => 'int',
                'value' => '0'
            ],
            [
                'key' => 'blocking_time',
                'type' => 'int',
                'value' => '0'
            ],
            [
                'key' => 'account_age_restriction',
                'type' => 'int',
                'value' => '0'
            ],
            [
                'key' => 'spam_timer',
                'type' => 'int',
                'value' => '0'
            ],
            [
                'key' => 'update_check',
                'type' => 'boolean',
                'value' => '0'
            ],
            [
                'key' => 'smtp_hostname',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'smtp_port',
                'type' => 'int',
                'value' => '0'
            ],
            [
                'key' => 'smtp_username',
                'type' => 'varchar',
                'value' => ''
            ],
            [
                'key' => 'smtp_password',
                'type' => 'varchar',
                'value' => ''
            ]
        ];

        $this->db->table('app_settings')->insertBatch($settings_data);

        // Add core group privileges
        $this->db->table('app_groups_privileges')->insertBatch([
            [
                'path' => 'addons',
                'privileges' => json_encode(['index', 'detail', 'install']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'addons/ftp',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'addons/modules',
                'privileges' => json_encode(['index', 'detail', 'import', 'update', 'delete']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'addons/themes',
                'privileges' => json_encode(['index', 'detail', 'import', 'update', 'delete', 'activate', 'customize']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/account',
                'privileges' => json_encode(['index', 'edit']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/account/logs',
                'privileges' => json_encode(['index', 'kick']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/logs',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/logs/activities',
                'privileges' => json_encode(['index', 'read', 'truncate', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/logs/errors',
                'privileges' => json_encode(['index', 'remove', 'clear']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/cleaner',
                'privileges' => json_encode(['index', 'clean']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/connections',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf', 'connect']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/countries',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/groups',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/groups/privileges',
                'privileges' => json_encode(['index', 'create', 'update', 'read', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/menus',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/settings',
                'privileges' => json_encode(['index', 'update']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/translations',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/translations/synchronize',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/translations/translate',
                'privileges' => json_encode(['index', 'delete_phrase']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/updater',
                'privileges' => json_encode(['index', 'update']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/users',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/users/privileges',
                'privileges' => json_encode(['index', 'update']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'administrative/years',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'apis',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'apis/debug_tool',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'apis/documentation',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'apis/services',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/blogs',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf', 'translate']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/blogs/categories',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/comments',
                'privileges' => json_encode(['index', 'read', 'export', 'print', 'pdf', 'hide']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/comments/feedback',
                'privileges' => json_encode(['index', 'read', 'export', 'print', 'pdf', 'hide']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/galleries',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/pages',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf', 'translate']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/announcements',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/carousels',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/faqs',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/inquiries',
                'privileges' => json_encode(['index', 'read', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/media',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/partials/testimonials',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/peoples',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'cms/videos',
                'privileges' => json_encode(['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'dashboard',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'notifier',
                'privileges' => json_encode(['index', 'read', 'export', 'print', 'pdf']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
            [
                'path' => 'notifier/setting',
                'privileges' => json_encode(['index']),
                'last_generated' => date('Y-m-d H:i:s')
            ],
        ]);

        // Add main user's group (superuser)
        $this->db->table('app_groups')->insert([
            'group_name' => 'Global Administrator',
            'group_description' => 'Super User',
            'group_privileges' => '{"addons":["index","detail","install"],"addons/ftp":["index"],"addons/modules":["index","detail","import","update","delete"],"addons/themes":["index","detail","import","update","delete","activate","customize"],"administrative":["index"],"administrative/account":["index","edit"],"administrative/account/logs":["index","kick"],"administrative/logs":["index"],"administrative/logs/activities":["index","read","truncate","delete","print","pdf"],"administrative/logs/errors":["index","remove","clear"],"administrative/cleaner":["index","clean"],"administrative/connections":["index","create","read","update","delete","export","print","pdf","connect"],"administrative/countries":["index","create","read","update","delete","export","print","pdf"],"administrative/groups":["index","create","read","update","delete","export","print","pdf"],"administrative/groups/privileges":["index","create","update","read","delete","export","print","pdf"],"administrative/menus":["index","create","read","update","delete","export","print","pdf"],"administrative/settings":["index","update"],"administrative/translations":["index","create","read","update","delete","export","print","pdf"],"administrative/translations/synchronize":["index"],"administrative/translations/translate":["index","delete_phrase"],"administrative/updater":["index","update"],"administrative/users":["index","create","read","update","delete","export","print","pdf"],"administrative/users/privileges":["index","update"],"administrative/years":["index","create","read","update","delete","export","print","pdf"],"apis":["index"],"apis/debug_tool":["index"],"apis/documentation":["index"],"apis/services":["index","create","read","update","delete","export","print","pdf"],"cms":["index"],"cms/blogs":["index","create","read","update","delete","export","print","pdf","translate"],"cms/blogs/categories":["index","create","read","update","delete","export","print","pdf"],"cms/comments":["index","read","export","print","pdf","hide"],"cms/comments/feedback":["index","read","export","print","pdf","hide"],"cms/galleries":["index","create","read","update","delete","export","print","pdf"],"cms/pages":["index","create","read","update","delete","export","print","pdf","translate"],"cms/partials":["index"],"cms/partials/announcements":["index","create","read","update","delete","export","print","pdf"],"cms/partials/carousels":["index","create","read","update","delete","export","print","pdf"],"cms/partials/faqs":["index","create","read","update","delete","export","print","pdf"],"cms/partials/inquiries":["index","read","delete","export","print","pdf"],"cms/partials/media":["index"],"cms/partials/testimonials":["index","create","read","update","delete","export","print","pdf"],"cms/peoples":["index","create","read","update","delete","export","print","pdf"],"cms/videos":["index","create","read","update","delete","export","print","pdf"],"dashboard":["index"],"notifier":["index","read","export","print","pdf"],"notifier/setting":["index"]}',
            'status' => 1
        ]);

        // Add technical user's group
        $this->db->table('app_groups')->insert([
            'group_name' => 'Technical',
            'group_description' => 'Group user for technical support',
            'group_privileges' => '{"administrative":["index"],"administrative/account":["index","edit"],"administrative/account/logs":["index","kick"],"apis":["index"],"apis/debug_tool":["index"],"apis/documentation":["index"],"apis/services":["index","create","read","update","delete","export","print","pdf"],"cms":["index"],"cms/blogs":["index","create","read","update","delete","export","print","pdf","translate"],"cms/blogs/categories":["index","create","read","update","delete","export","print","pdf"],"cms/comments":["index","read","export","print","pdf","hide"],"cms/comments/feedback":["index","read","export","print","pdf","hide"],"cms/galleries":["index","create","read","update","delete","export","print","pdf"],"cms/pages":["index","create","read","update","delete","export","print","pdf","translate"],"cms/partials":["index"],"cms/partials/announcements":["index","create","read","update","delete","export","print","pdf"],"cms/partials/carousels":["index","create","read","update","delete","export","print","pdf"],"cms/partials/faqs":["index","create","read","update","delete","export","print","pdf"],"cms/partials/inquiries":["index","read","delete","export","print","pdf"],"cms/partials/media":["index"],"cms/partials/testimonials":["index","create","read","update","delete","export","print","pdf"],"cms/peoples":["index","create","read","update","delete","export","print","pdf"],"cms/videos":["index","create","read","update","delete","export","print","pdf"],"dashboard":["index"],"notifier":["index","read","export","print","pdf"]}',
            'status' => 1
        ]);

        // Add subscriber user's group
        $this->db->table('app_groups')->insert([
            'group_name' => 'Subscriber',
            'group_description' => 'Group user for subscriber',
            'group_privileges' => '{"administrative":["index"],"administrative/account":["index","edit"],"administrative/account/logs":["index","kick"],"dashboard":["index"]}',
            'status' => 1
        ]);

        // Insert superuser
        $this->db->table('app_users')->insert([
            'email' => session()->get('email'),
            'password' => password_hash(session()->get('password') . session()->get('encryption'), PASSWORD_DEFAULT),
            'username' => session()->get('username'),
            'first_name' => session()->get('first_name'),
            'last_name' => session()->get('last_name'),
            'gender' => 0,
            'bio' => '',
            'photo' => '',
            'address' => '',
            'phone' => '',
            'postal_code' => '',
            'language_id' => (session()->get('language') == 'id' ? 2 : 1),
            'country_id' => 0,
            'group_id' => 1,
            'registered_date' => date('Y-m-d'),
            'last_login' => date('Y-m-d H:i:s'),
            'status' => 1
        ]);
    }
}
