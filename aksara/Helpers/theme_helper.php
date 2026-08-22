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

if (! function_exists('asset_loader')) {
    /**
     * Load additional CSS or JS files efficiently.
     * * @param array|string $assets Array or comma-separated string of file paths
     */
    function asset_loader(string|array $assets = []): string
    {
        // Use the optimized helper instead of debug_backtrace
        $theme = get_theme();

        // Fallback: from settings if helper returns null
        if (! $theme) {
            $theme = get_setting('frontend_theme') ?? 'default';
        }

        // Normalize assets to array
        if (! is_array($assets)) {
            $assets = array_map('trim', explode(',', $assets));
        }

        $output = '';

        foreach ($assets as $val) {
            $val = trim($val);
            if (empty($val)) {
                continue;
            }

            $extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
            $fileUrl = '';

            // Priority 1: Check theme assets directory
            if (file_exists(ROOTPATH . 'themes/' . $theme . '/assets/' . $val)) {
                $fileUrl = base_url('themes/' . $theme . '/assets/' . $val);
            }

            // Priority 2: Fallback to core assets directory
            elseif (file_exists(FCPATH . 'assets/' . $val)) {
                $fileUrl = base_url('assets/' . $val);
            }

            // Generate HTML tags based on extension
            if ($fileUrl) {
                if ('css' === $extension) {
                    $output .= '<link rel="stylesheet" type="text/css" href="' . $fileUrl . '" />' . "\n";
                } elseif ('js' === $extension) {
                    $output .= '<script type="text/javascript" src="' . $fileUrl . '"></script>' . "\n";
                }
            } else {
                log_message('warning', "Asset not found: {$val} (theme: {$theme})");
            }
        }

        return $output;
    }
}

if (! function_exists('get_theme_asset')) {
    /**
     * Load theme asset.
     * The file location is directed to a folder named "assets" within the active theme.
     */
    function get_theme_asset(string $data): string
    {
        // Use the optimized helper instead of scanning backtrace
        $theme = get_theme();

        if ($theme) {
            // Define the local path and web URL
            $assetPath = ROOTPATH . 'themes/' . $theme . '/assets/' . $data;

            // Check if the file exists within the theme's asset directory
            if (file_exists($assetPath)) {
                // Return the cleaned base URL
                return str_replace('/index.php/', '/', base_url('themes/' . $theme . '/assets/' . $data));
            }
        }

        // Return a dummy link if theme is not detected or file doesn't exist
        return '#';
    }
}

if (! function_exists('get_module_asset')) {
    /**
     * Load a module-specific asset.
     *
     * This function identifies the active module by parsing the current controller's
     * namespace via the router service. It checks for the file's existence within
     * the 'assets' directory of both user-defined modules and core modules.
     */
    function get_module_asset(?string $data = null): string
    {
        // Get the fully qualified class name of the current controller
        $controller = service('router')->controllerName();

        // Extract the module name using regex to capture the segment between \Modules\ and \Controllers\
        preg_match('/\\\Modules\\\(.*?)\\\Controllers\\\/', $controller, $matches);

        // Get the captured module name from the regex matches
        $module = $matches[1] ?? null;

        if ($module) {
            /**
             * Check if the asset exists in:
             * 1. The custom modules directory (/modules/...)
             * 2. The core Aksara modules directory (/aksara/Modules/...)
             */
            if (
                file_exists(ROOTPATH . "modules/$module/assets/$data")
                || file_exists(APPPATH . "Modules/$module/assets/$data")
            ) {
                // Return the base URL pointing to the assets directory
                return base_url("modules/$module/assets/$data");
            }
        }

        // Return a dummy link if the module cannot be determined or the file is missing
        return '#';
    }
}

if (! function_exists('theme_config')) {
    /**
     * Decode and parse the user's theme configuration cookie.
     */
    function theme_config(): ?array
    {
        helper('cookie');

        $rawThemeConfig = get_cookie('aksara_theme_config') ?? ($_COOKIE['aksara_theme_config'] ?? null);

        if (! $rawThemeConfig) {
            return null;
        }

        $sanitizedConfig = str_replace(' ', '+', urldecode((string) $rawThemeConfig));
        $decodedConfig = base64_decode($sanitizedConfig);

        if (! $decodedConfig) {
            return null;
        }

        $parsed = json_decode($decodedConfig, true);

        return is_array($parsed) ? $parsed : null;
    }
}

if (! function_exists('theme_mode')) {
    /**
     * Get active theme mode (light or dark).
     */
    function theme_mode(?array $theme = null): string
    {
        if (is_array($theme) && ! empty($theme['activeMode'])) {
            return $theme['activeMode'];
        }

        helper('cookie');
        $rawThemeMode = get_cookie('aksara_theme') ?? ($_COOKIE['aksara_theme'] ?? null);

        return $rawThemeMode ?: 'light';
    }
}

if (! function_exists('compile_theme')) {
    /**
     * Compile user theme overrides into CSS rulesets.
     */
    function compile_theme(?array $theme = null): ?string
    {
        if (! is_array($theme) || empty($theme['overrides']) || empty($theme['activeMode'])) {
            return null;
        }

        $map = [
            'primary' => ['--wg-primary', '--bs-primary', '--bs-link-color'],
            'secondary' => ['--wg-text-light', '--bs-secondary'],
            'accent' => ['--wg-primary-hover', '--bs-link-hover-color', '--bs-accent', '--range-color'],
            'background' => ['--bs-body-bg'],
            'surface' => ['--bs-surface-bg', '--bs-sidebar-bg'],
            'foreground' => ['--wg-text', '--bs-body-color', '--bs-emphasis-color'],
            'muted' => ['--wg-bg-light', '--bs-breadcrumb-bg'],
            'secondaryBg' => ['--bs-secondary-bg'],
            'tertiaryBg' => ['--bs-tertiary-bg'],
            'border' => ['--wg-border', '--bs-border-color', '--range-track-border'],
            'success' => ['--bs-success'],
            'info' => ['--bs-info'],
            'warning' => ['--bs-warning'],
            'danger' => ['--bs-danger']
        ];

        $rgbMap = [
            'primary' => ['--bs-primary-rgb'],
            'secondary' => ['--bs-secondary-rgb'],
            'accent' => ['--bs-accent-rgb'],
            'success' => ['--bs-success-rgb'],
            'info' => ['--bs-info-rgb'],
            'warning' => ['--bs-warning-rgb'],
            'danger' => ['--bs-danger-rgb'],
            'background' => ['--bs-body-bg-rgb'],
            'foreground' => ['--bs-body-color-rgb', '--bs-emphasis-color-rgb'],
            'secondaryBg' => ['--bs-secondary-bg-rgb'],
            'tertiaryBg' => ['--bs-tertiary-bg-rgb']
        ];

        $blocks = [];

        foreach (['light', 'dark'] as $mode) {
            $colors = $theme['overrides'][$mode] ?? [];
            $css = [];

            foreach ($map as $token => $variables) {
                if (! empty($colors[$token]) && preg_match('/^#[0-9a-fA-F]{6}$/', $colors[$token])) {
                    foreach ($variables as $variable) {
                        $css[] = $variable . ':' . strtolower($colors[$token]);
                    }
                }
            }

            foreach ($rgbMap as $token => $variables) {
                if (! empty($colors[$token]) && preg_match('/^#[0-9a-fA-F]{6}$/', $colors[$token])) {
                    $rgb = implode(', ', sscanf($colors[$token], '#%02x%02x%02x'));
                    foreach ($variables as $variable) {
                        $css[] = $variable . ':' . $rgb;
                    }
                }
            }

            if ($css) {
                $blocks[] = '[data-bs-theme="' . $mode . '"]{' . implode(';', $css) . '}';
            }
        }

        return $blocks ? implode('', $blocks) : null;
    }
}

if (! function_exists('theme_color')) {
    /**
     * Get theme primary color for browser status bar.
     */
    function theme_color(?array $theme = null, ?string $mode = null, ?string $themeName = null): string
    {
        $mode = $mode ?: theme_mode($theme);

        if (is_array($theme) && ! empty($theme['overrides'][$mode]['primary']) && preg_match('/^#[0-9a-fA-F]{6}$/', $theme['overrides'][$mode]['primary'])) {
            return strtolower($theme['overrides'][$mode]['primary']);
        }

        $baseTheme = is_array($theme) ? ($theme['baseTheme'] ?? 'default') : 'default';

        if (! $themeName) {
            $themeName = get_theme() ?: 'default';
        }

        $configPath = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $themeName . DIRECTORY_SEPARATOR . 'theme.json';
        $fallbackPath = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'theme.json';

        $themeConfig = is_file($configPath) ? json_decode((string) file_get_contents($configPath), true) : [];

        if (empty($themeConfig['presets']) && is_file($fallbackPath)) {
            $themeConfig = json_decode((string) file_get_contents($fallbackPath), true);
        }

        $presets = $themeConfig['presets'] ?? [];
        $selectedPreset = null;

        foreach ($presets as $preset) {
            if (($preset['id'] ?? '') === $baseTheme) {
                $selectedPreset = $preset;
                break;
            }
        }

        if (! $selectedPreset && ! empty($presets[0])) {
            $selectedPreset = $presets[0];
        }

        return $selectedPreset['colors'][$mode]['primary'] ?? ('dark' === $mode ? '#5f7fa6' : '#1e3a5f');
    }
}
