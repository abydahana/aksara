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

use CodeIgniter\CodeIgniter;
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
        $version = '5.2.5';

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
     * Fetches a specific field from the 'app__settings' table.
     *
     * @param   string $parameter The column name to retrieve
     * @return  mixed Returns the setting value or null if not found
     */
    function get_setting(string $parameter): string
    {
        $model = new Model();

        if ($model->fieldExists($parameter, 'app__settings')) {
            return $model->select($parameter)->getWhere(
                'app__settings',
                [
                    'id' => 1
                ],
                1
            )
            ->row($parameter);
        }

        return '';
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
            $model = new Model();
            $userId = service('session')->get('user_id');

            // Attempt to fetch from privileges table first
            if ($model->fieldExists($field, 'app__users_privileges')) {
                return $model->select($field)->getWhere(
                    'app__users_privileges',
                    [
                        'user_id' => $userId
                    ],
                    1
                )
                ->row($field);
            }
            // Attempt to fetch from main users table
            elseif ($model->fieldExists($field, 'app__users')) {
                return $model->select(
                    $field
                )
                ->getWhere(
                    'app__users',
                    [
                        'user_id' => $userId
                    ],
                    1
                )
                ->row($field);
            }

            return null;
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

        $model = new Model();

        // Sanitize and Normalize
        $phrase = preg_replace('/[^\w\s\p{P}\p{L}]/u', ' ', $phrase);
        $phrase = str_replace(['[', ']'], ['(', ')'], $phrase);
        $phrase = preg_replace('/\s+/', ' ', $phrase);
        $phrase = trim($phrase);

        // 1. Determine Language
        $language = get_userdata('language');

        if (! $language) {
            $appLanguage = get_setting('app_language');
            $languageId = (get_userdata('language_id') ? get_userdata('language_id') : ($appLanguage > 0 ? $appLanguage : 1));

            $language = $model->select('code')
            ->getWhere(
                'app__languages',
                [
                    'id' => $languageId
                ]
            )
            ->row('code');
        }

        // 2. File Handling
        $translationFile = WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $language . '.json';

        if (! file_exists($translationFile)) {
            if (! is_dir(WRITEPATH . 'translations')) {
                try {
                    mkdir(WRITEPATH . 'translations', 0755, true);
                    file_put_contents($translationFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } catch (Throwable $e) {
                    log_message('error', '[TRANSLATION] ' . $e->getMessage());
                }
            } elseif (is_writable(WRITEPATH . 'translations')) {
                try {
                    file_put_contents($translationFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } catch (Throwable $e) {
                    log_message('error', '[TRANSLATION] ' . $e->getMessage());
                }
            }
        }

        try {
            // 3. Process Translation
            $buffer = file_get_contents($translationFile);
            $phrases = (is_json($buffer) ? json_decode($buffer, true) : []);

            if (! is_array($phrases)) {
                $phrases = [];
            }

            if (! isset($phrases[$phrase]) && ! $checking) {
                // Only append new phrase if checking is false
                $phrases[$phrase] = $phrase;

                // Sort phrases by key
                ksort($phrases);

                if (file_exists($translationFile) && is_writable($translationFile)) {
                    // No translation exists
                    $jsonContent = json_encode(
                        $phrases,
                        JSON_PRETTY_PRINT |
                        JSON_UNESCAPED_SLASHES |
                        JSON_UNESCAPED_UNICODE
                    );

                    // Create new translation file
                    file_put_contents($translationFile, $jsonContent, LOCK_EX);
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
        } catch (Throwable $e) {
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

if (! function_exists('is_rtl')) {
    /**
     * Check if the current language uses Right-to-Left (RTL) script.
     */
    function is_rtl(): bool
    {
        return in_array(get_userdata('language'), [
            'ar', 'arc', 'dv', 'fa', 'ha', 'he', 'khw',
            'ks', 'ku', 'ps', 'ur', 'yi', 'sd', 'ug',
        ], true);
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
        $model = new Model();

        return $model->getWhere(
            'post__likes',
            [
                'user_id' => get_userdata('user_id'),
                'post_id' => $postId,
                'post_path' => $postPath
            ],
            1
        )
        ->numRows() > 0;
    }
}
