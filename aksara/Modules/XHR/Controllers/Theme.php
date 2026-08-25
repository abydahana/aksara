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

namespace Aksara\Modules\XHR\Controllers;

use Aksara\Laboratory\Core;

class Theme extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->permission->mustAjax();
    }

    public function index()
    {
        return $this->editor();
    }

    public function toggle()
    {
        $theme = $this->request->getPost('theme') ?? $this->request->getGet('theme');

        if (in_array($theme, ['dark', 'light'], true)) {
            helper('cookie');
            $expiration = SESSION_EXPIRATION ?: (86400 * 365);
            set_cookie('aksara_theme', $theme, $expiration, '', '/', '', false, false);

            $rawThemeConfig = get_cookie('aksara_theme_config') ?? ($_COOKIE['aksara_theme_config'] ?? null);

            if ($rawThemeConfig) {
                $sanitizedConfig = str_replace(' ', '+', urldecode((string) $rawThemeConfig));
                $decodedConfig = base64_decode($sanitizedConfig);

                if ($decodedConfig) {
                    $decodedTheme = json_decode($decodedConfig, true);

                    if (is_array($decodedTheme)) {
                        $decodedTheme['activeMode'] = $theme;
                        $encodedConfig = rawurlencode(base64_encode(json_encode($decodedTheme)));
                        set_cookie('aksara_theme_config', $encodedConfig, $expiration, '', '/', '', false, false);
                    }
                }
            }
        }

        return make_json([
            'status' => 200,
            'theme' => $theme
        ]);
    }

    public function save()
    {
        $theme = json_decode((string) $this->request->getPost('theme'), true);

        if (! is_array($theme)) {
            return make_json([
                'status' => 400,
                'message' => phrase('Invalid theme configuration.')
            ]);
        }

        $activeMode = $theme['activeMode'] ?? 'light';
        $baseTheme = $theme['baseTheme'] ?? 'default';
        $allowedTokens = [
            'background',
            'surface',
            'foreground',
            'primary',
            'secondary',
            'accent',
            'muted',
            'secondaryBg',
            'tertiaryBg',
            'border',
            'success',
            'info',
            'warning',
            'danger'
        ];
        $output = [
            'baseTheme' => preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $baseTheme) ?: 'default',
            'activeMode' => in_array($activeMode, ['dark', 'light'], true) ? $activeMode : 'light',
            'overrides' => [
                'light' => [],
                'dark' => []
            ]
        ];

        foreach (['light', 'dark'] as $mode) {
            foreach (($theme['overrides'][$mode] ?? []) as $token => $value) {
                if (in_array($token, $allowedTokens, true) && is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                    $output['overrides'][$mode][$token] = strtolower($value);
                }
            }
        }

        helper('cookie');
        $expiration = SESSION_EXPIRATION ?: (86400 * 365);
        $encodedConfig = rawurlencode(base64_encode(json_encode($output)));
        set_cookie('aksara_theme_config', $encodedConfig, $expiration, '', '/', '', false, false);
        set_cookie('aksara_theme', $output['activeMode'], $expiration, '', '/', '', false, false);

        return throw_exception(301, [
            'message' => phrase('Theme saved successfully.'),
            'data' => [
                'theme' => $output
            ]
        ]);
    }

    public function editor()
    {
        $theme = $this->request->getPost('theme') ?? $this->request->getGet('theme') ?? 'default';
        $theme = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $theme) ?: 'default';
        $view = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'editor.php';

        if (! is_file($view)) {
            $theme = 'default';
            $view = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'editor.php';
        }

        if (! is_file($view)) {
            return throw_exception(404, phrase('The page you requested does not exist or has already been archived.'));
        }

        ob_start();
        include $view;
        $content = ob_get_clean();

        return make_json([
            'meta' => [
                'popup' => true,
                'title' => phrase('Theme Editor'),
                'icon' => 'mdi mdi-palette-outline',
                'modal_size' => 'modal-lg'
            ],
            'content' => $content
        ]);
    }
}
