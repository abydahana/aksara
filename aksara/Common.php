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

use CodeIgniter\CodeIgniter;
use CodeIgniter\View\View;
use Config\Services;
use Aksara\Laboratory\Model;

if (! function_exists('aksara')) {
    /**
     * Retrieve Aksara CMS core information.
     * Used to get version number, build info, or core modification date.
     *
     * @param   string $parameter Can be 'version', 'build_version', or 'date_modified'
     * @return  string Returns specific info string or empty string if parameter invalid
     */
    function aksara(string $parameter): string
    {
        $version = '6.3.5';

        if ('version' == $parameter) {
            return $version;
        } elseif ('build_version' == $parameter) {
            return $version . CodeIgniter::CI_VERSION;
        } elseif ('date_modified' == $parameter) {
            $modified = filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php');

            return date('F d Y H:i:s', $modified);
        }

        return '';
    }
}

if (! function_exists('get_setting')) {
    /**
     * Retrieve application setting value from database.
     * Fetches a specific field from the 'app_settings' table.
     *
     * @param   string $parameter The column name to retrieve
     * @param   mixed  $default   The default value to return if the setting is not found
     */
    function get_setting(string $parameter, $default = null): mixed
    {
        static $settings = null;

        if (null === $settings) {
            $cache = service('cache');
            $settings = $cache->get('aksara_app_settings');

            if (! is_array($settings)) {
                $settings = [];

                try {
                    $model = new Model();
                    $rows = $model->getWhere('app_settings')->resultArray();

                    foreach ($rows as $row) {
                        if (isset($row['key'])) {
                            $settings[$row['key']] = $row['value'] ?? '';
                        }
                    }

                    $cache->save('aksara_app_settings', $settings, 300);
                } catch (\Throwable $e) {
                    log_message('error', 'Unable to load application settings: ' . $e->getMessage());
                }
            }
        }

        return $settings[$parameter] ?? $default;
    }
}

if (! function_exists('get_userdata')) {
    /**
     * Retrieve user session data.
     * Prioritizes session storage, falls back to database lookup.
     *
     * @param   string $field The key to retrieve
     * @return  mixed Returns session data or null
     */
    function get_userdata(string $field = ''): mixed
    {
        // Check if data is missing in session but user is logged in
        if (! service('session')->get($field) && service('session')->get('user_id')) {
            try {
                $model = new Model();
                $userId = service('session')->get('user_id');

                // Attempt to fetch from privileges table first
                if ($model->fieldExists($field, 'app_users_privileges')) {
                    return $model->select($field)->getWhere(
                        'app_users_privileges',
                        [
                            'user_id' => $userId
                        ],
                        1
                    )
                    ->row($field);
                }
                // Attempt to fetch from main users table
                elseif ($model->fieldExists($field, 'app_users')) {
                    return $model->select($field)->getWhere(
                        'app_users',
                        [
                            'user_id' => $userId
                        ],
                        1
                    )
                    ->row($field);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Unable to retrieve user data: ' . $e->getMessage());
            }
        }

        return service('session')->get($field);
    }
}

if (! function_exists('set_userdata')) {
    /**
     * Set user session data.
     * $key is mandatory. $value is optional only if $key is an array.
     *
     * @param   array|string $key Key or array of key-value pairs
     * @param   mixed|null   $value Value (required if key is string)
     */
    function set_userdata(array|string $key, mixed $value = null): void
    {
        if (! is_array($key)) {
            $key = [
                $key => $value
            ];
        }

        service('session')->set($key);
    }
}

if (! function_exists('unset_userdata')) {
    /**
     * Unset user session data.
     *
     * @param   array|string $key Key or array of keys to remove
     */
    function unset_userdata(array|string $key): void
    {
        service('session')->remove($key);
    }
}

if (! function_exists('load_translations')) {
    /**
     * Load and merge translations for a language.
     *
     * Translations are loaded from the core file and every module-scope file,
     * then cached per request and under writable/cache/translations.
     *
     * @param   string $language Language code
     * @param   bool   $clear Clear static cache instead of loading translations
     * @return  array Merged phrase map
     */
    function load_translations(string $language, bool $clear = false): array
    {
        static $translations = [];

        if ($clear) {
            if ('*' === $language) {
                $translations = [];
            } else {
                unset($translations[$language]);
            }

            return [];
        }

        if (isset($translations[$language])) {
            return $translations[$language];
        }

        $phrases = [];
        $base = WRITEPATH . 'translations';
        $files = [translation_file($language)];

        foreach (glob($base . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $language . '.json') ?: [] as $file) {
            $files[] = $file;
        }

        $cachePath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'translations';
        $cacheFile = $cachePath . DIRECTORY_SEPARATOR . $language . '.json';

        try {
            foreach (glob($cachePath . DIRECTORY_SEPARATOR . $language . '-*.json') ?: [] as $file) {
                @unlink($file);
            }

            if (file_exists($cacheFile) && ! translations_cache_is_stale($cacheFile, $files)) {
                $cached = json_decode(file_get_contents($cacheFile) ?: '[]', true);

                if (is_array($cached)) {
                    return $translations[$language] = $cached;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[TRANSLATION] Unable to load translation cache: ' . $e->getMessage());
        }

        foreach ($files as $file) {
            if (! file_exists($file)) {
                continue;
            }

            $buffer = file_get_contents($file);
            $decoded = json_decode($buffer ?: '[]', true);

            if (JSON_ERROR_NONE === json_last_error() && is_array($decoded)) {
                $phrases += $decoded;
            }
        }

        try {
            if (! is_dir($cachePath)) {
                mkdir($cachePath, 0755, true);
            }

            file_put_contents($cacheFile, json_encode($phrases, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
        } catch (\Throwable $e) {
            log_message('error', '[TRANSLATION] Unable to save translation cache: ' . $e->getMessage());
        }

        return $translations[$language] = $phrases;
    }
}

if (! function_exists('clear_translations_cache')) {
    /**
     * Clear in-request translation cache.
     *
     * Passing a language clears only that language. Passing null clears all
     * loaded language caches for the current request.
     *
     * @param   string|null $language Language code
     */
    function clear_translations_cache(?string $language = null): void
    {
        load_translations($language ?: '*', true);
    }
}

if (! function_exists('translations_cache_is_stale')) {
    /**
     * Check whether a translation cache file is older than its source files.
     *
     * @param   string $cacheFile Cache file path
     * @param   array  $sourceFiles Translation/source file paths
     */
    function translations_cache_is_stale(string $cacheFile, array $sourceFiles): bool
    {
        $cacheTime = filemtime($cacheFile);

        foreach ($sourceFiles as $file) {
            if (file_exists($file) && filemtime($file) > $cacheTime) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('phrase')) {
    /**
     * Translate a string/phrase.
     *
     * @param   string $phrase The string to translate (Required)
     * @param   array  $replacement Associative array for variable replacement
     * @param   bool  $checking Only use existing translation without append new phrase
     * @return  string Returns translated string or empty string if input invalid
     */
    function phrase(string $phrase, ?array $replacement = [], bool $checking = false): string
    {
        mb_internal_encoding('UTF-8');

        // Since type hint is string, we just check for numeric string or empty
        if (is_numeric($phrase)) {
            // Cast numeric string to ensure string manipulation works
            $phrase = (string) $phrase;
        }

        if (empty($phrase)) {
            return '';
        }

        // Sanitize and Normalize
        $phrase = preg_replace('/[^\w\s\p{P}\p{L}]/u', ' ', $phrase);
        $phrase = str_replace(['[', ']'], ['(', ')'], $phrase);
        $phrase = preg_replace('/\s+/', ' ', $phrase);
        $phrase = trim($phrase);

        // 1. Determine Language
        $language = get_userdata('language');

        if (! $language) {
            try {
                $model = new Model();
                $appLanguage = get_setting('app_language');
                $languageId = (get_userdata('language_id') ? get_userdata('language_id') : ($appLanguage > 0 ? $appLanguage : 1));

                $language = $model->select('code')
                ->getWhere(
                    'app_languages',
                    [
                        'id' => $languageId
                    ]
                )
                ->row('code');
            } catch (\Throwable $e) {
                log_message('error', 'Unable to retrieve language code: ' . $e->getMessage());
            }
        }

        try {
            // 2. Process Translation
            $phrases = load_translations($language);

            if (! isset($phrases[$phrase]) && ! $checking) {
                // Only append new phrase if checking is false
                $translationFile = translation_file($language, translation_scope_from_trace());
                $scopedPhrases = [];

                if (file_exists($translationFile)) {
                    $scopedBuffer = file_get_contents($translationFile);
                    $scopedPhrases = json_decode($scopedBuffer ?: '[]', true);
                }

                if (! is_array($scopedPhrases)) {
                    $scopedPhrases = [];
                }

                $scopedPhrases[$phrase] = $phrase;
                $phrases[$phrase] = $phrase;

                // Sort phrases by key
                ksort($scopedPhrases);

                if (! is_dir(dirname($translationFile))) {
                    mkdir(dirname($translationFile), 0755, true);
                }

                if ((! file_exists($translationFile) && is_writable(dirname($translationFile))) || is_writable($translationFile)) {
                    // No translation exists
                    $jsonContent = json_encode(
                        $scopedPhrases,
                        JSON_PRETTY_PRINT |
                        JSON_UNESCAPED_SLASHES |
                        JSON_UNESCAPED_UNICODE
                    );

                    // Create new translation file
                    file_put_contents($translationFile, $jsonContent, LOCK_EX);
                    clear_translations_cache($language);
                }
            }

            if ($checking) {
                // Only check, use existing translation
                $phrasesReversed = array_reverse($phrases, true);
                $phrasesUpper = array_change_key_case($phrasesReversed, CASE_UPPER);
                $upperPhrase = strtoupper($phrase);
                $translatedPhrase = (isset($phrasesUpper[$upperPhrase]) ? $phrasesUpper[$upperPhrase] : $phrase);
            } else {
                // Try to using existing or appended phrase
                $translatedPhrase = (isset($phrases[$phrase]) ? $phrases[$phrase] : $phrase);
            }

            // Typographical beautification
            $translatedPhrase = preg_replace('/"([^"]+)"/', '“$1”', $translatedPhrase);
            $translatedPhrase = str_replace(['`', "'"], '’', $translatedPhrase);

            $phrase = $translatedPhrase;
        } catch (\Throwable $e) {
            log_message('error', '[TRANSLATION] ' . $e->getMessage());
        }

        // 4. Replacements
        if ($replacement) {
            foreach ($replacement as $keyword => $replace) {
                $phrase = preg_replace("/\{\{(\s+)?(" . preg_quote($keyword, '/') . ")(\s+)?\}\}/", $replace, $phrase);
            }
        }

        return $phrase;
    }
}

if (! function_exists('user_language')) {
    // Get language from user session
    function get_user_language()
    {
        try {
            $model = new Model();

            return $model->getWhere(
                'app_languages',
                [
                    'code' => get_userdata('language')
                ],
                1
            )
            ->row('language');
        } catch (\Throwable $e) {
            log_message('error', 'Unable to retrieve user language: ' . $e->getMessage());
        }

        return null;
    }
}

if (! function_exists('get_active_years')) {
    /**
     * Get active years from database.
     *
     * @return object Returns object of active years
     */
    function get_active_years()
    {
        try {
            $model = new Model();

            return $model->getWhere(
                'app_years',
                [
                    'status' => 1
                ]
            )
            ->result();
        } catch (\Throwable $e) {
            log_message('error', 'Unable to retrieve active years: ' . $e->getMessage());
        }

        return [];
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Check if the current language uses Right-to-Left (RTL) script.
     */
    function is_rtl(): bool
    {
        return in_array(get_userdata('language'), [
            'ar', 'arc', 'dv', 'fa', 'ha', 'he', 'khw',
            'ks', 'ku', 'ps', 'ur', 'yi', 'sd', 'ug',
        ]);
    }
}

if (! function_exists('is_liked')) {
    /**
     * Check if a post has been liked by the current user.
     *
     * @param   int         $postId The ID of the post (Required)
     * @param   string|null $postPath The path/type of the post
     */
    function is_liked(int $postId, ?string $postPath = null): bool
    {
        try {
            $model = new Model();

            return $model->getWhere(
                'post_likes',
                [
                    'created_by' => get_userdata('user_id'),
                    'post_id' => $postId,
                    'post_path' => $postPath
                ],
                1
            )
            ->numRows() > 0;
        } catch (\Throwable $e) {
            log_message('error', 'Unable to check if post is liked: ' . $e->getMessage());
        }

        return false;
    }
}

if (! function_exists('translation_scope_from_path')) {
    /**
     * Resolve translation scope from a source file path.
     *
     * Files under modules/{ModuleName} are scoped to the lowercase module name.
     * All other files are treated as core translations.
     *
     * @param   string|null $path Source file path
     * @return  string Translation scope name
     */
    function translation_scope_from_path(?string $path = null): string
    {
        $path = $path ?: '';
        $normalized = str_replace('\\', '/', $path);
        $root = rtrim(str_replace('\\', '/', ROOTPATH), '/') . '/';

        if ($root && str_starts_with($normalized, $root)) {
            $normalized = substr($normalized, strlen($root));
        }

        if (preg_match('#^modules/([^/]+)/#i', $normalized, $match)) {
            return strtolower($match[1]);
        }

        return 'core';
    }
}

if (! function_exists('translation_scope_from_trace')) {
    /**
     * Resolve translation scope from the current call stack.
     *
     * This is used by phrase() to append new phrases into the module file
     * that originally called it.
     *
     * @return  string Translation scope name
     */
    function translation_scope_from_trace(): string
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 12) as $trace) {
            if (! empty($trace['file'])) {
                $scope = translation_scope_from_path($trace['file']);

                if ('core' !== $scope) {
                    return $scope;
                }
            }
        }

        return 'core';
    }
}

if (! function_exists('translation_file')) {
    /**
     * Build writable translation file path for a language and scope.
     *
     * Core translations are stored at writable/translations/{language}.json,
     * while module translations are stored at writable/translations/{scope}/{language}.json.
     *
     * @param   string $language Language code
     * @param   string $scope Translation scope name
     * @return  string Absolute translation file path
     */
    function translation_file(string $language, string $scope = 'core'): string
    {
        return WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . ('core' === $scope ? '' : $scope . DIRECTORY_SEPARATOR) . $language . '.json';
    }
}

if (! function_exists('view')) {
    /**
     * Render a view template.
     *
     * Overrides CodeIgniter's view() helper to add automatic case-convention resolution
     * (e.g. blogBook vs blog_book vs blog-book vs BlogBook) if the exact view path is not found.
     *
     * @param   string $name View file name or path
     * @param   array  $data Data array to pass to view
     * @param   array  $options Options array
     * @return  string Rendered view HTML string
     */
    function view(string $name, array $data = [], array $options = []): string
    {
        /** @var View $renderer */
        $renderer = Services::renderer();

        $config = config('View');
        $saveData = $config?->saveData ?? true;

        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        $locator = Services::locator();

        // Check if exact view name exists
        if (! $locator->locateFile($name, 'Views')) {
            $delimiter = str_contains($name, '\\') ? '\\' : '/';
            $parts = explode($delimiter, $name);
            $lastPart = array_pop($parts);
            $prefix = $parts ? implode($delimiter, $parts) . $delimiter : '';

            $words = str_replace(['_', '-'], ' ', preg_replace('/(?<!^)[A-Z]/', ' $0', $lastPart));
            $words = preg_replace('/\s+/', ' ', trim($words));

            $snakePart = strtolower(str_replace(' ', '_', $words));
            $kebabPart = strtolower(str_replace(' ', '-', $words));
            $camelPart = lcfirst(str_replace(' ', '', ucwords(strtolower($words))));
            $pascalPart = str_replace(' ', '', ucwords(strtolower($words)));

            $candidates = array_unique([
                $prefix . $snakePart,
                $prefix . $kebabPart,
                $prefix . $camelPart,
                $prefix . $pascalPart,
            ]);

            foreach ($candidates as $candidate) {
                if ($locator->locateFile($candidate, 'Views')) {
                    $name = $candidate;

                    break;
                }
            }
        }

        return $renderer->setData($data, 'raw')->render($name, $options, $saveData);
    }
}
