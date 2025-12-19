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
        $version = '5.2.4';

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

        if ($model->field_exists($parameter, 'app__settings')) {
            return $model->select($parameter)->get_where(
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
            $user_id = service('session')->get('user_id');

            // Attempt to fetch from privileges table first
            if ($model->field_exists($field, 'app__users_privileges')) {
                return $model->select($field)->get_where(
                    'app__users_privileges',
                    [
                        'user_id' => $user_id
                    ],
                    1
                )
                ->row($field);
            }
            // Attempt to fetch from main users table
            elseif ($model->field_exists($field, 'app__users')) {
                return $model->select(
                    $field
                )
                ->get_where(
                    'app__users',
                    [
                        'user_id' => $user_id
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
     * @return  string Returns translated string or empty string if input invalid
     */
    function phrase(string $phrase, array $replacement = []): string
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
            $app_language = get_setting('app_language');
            $language_id = (get_userdata('language_id') ? get_userdata('language_id') : ($app_language > 0 ? $app_language : 1));

            $language = $model->select('code')
            ->get_where(
                'app__languages',
                [
                    'id' => $language_id
                ]
            )
            ->row('code');
        }

        // 2. File Handling
        $translation_file = WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $language . '.json';

        if (! file_exists($translation_file)) {
            if (! is_dir(WRITEPATH . 'translations')) {
                try {
                    mkdir(WRITEPATH . 'translations', 0755, true);
                    file_put_contents($translation_file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } catch (Throwable $e) {
                }
            } elseif (is_writable(WRITEPATH . 'translations')) {
                try {
                    file_put_contents($translation_file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } catch (Throwable $e) {
                }
            }
        }

        try {
            // 3. Process Translation
            $buffer = file_get_contents($translation_file);
            $phrases = (is_json($buffer) ? json_decode($buffer, true) : []);

            if (! is_array($phrases)) {
                $phrases = [];
            }

            if (! isset($phrases[$phrase])) {
                $phrases[$phrase] = $phrase;
                ksort($phrases);

                if (file_exists($translation_file) && is_writable($translation_file)) {
                    $json_content = json_encode(
                        $phrases,
                        JSON_PRETTY_PRINT |
                        JSON_UNESCAPED_SLASHES |
                        JSON_UNESCAPED_UNICODE
                    );
                    file_put_contents($translation_file, $json_content, LOCK_EX);
                }
            }

            $translated_phrase = $phrases[$phrase] ?? $phrase;

            // Typographical beautification
            $translated_phrase = preg_replace('/"([^"]+)"/', '“$1”', $translated_phrase);
            $translated_phrase = str_replace(['`', "'"], '’', $translated_phrase);

            $phrase = $translated_phrase;
        } catch (Throwable $e) {
            // Keep original on error
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
        ]);
    }
}

if (! function_exists('is_liked')) {
    /**
     * Check if a post has been liked by the current user.
     *
     * @param   int         $post_id The ID of the post (Required)
     * @param   string|null $post_path The path/type of the post
     */
    function is_liked(int $post_id, ?string $post_path = null): bool
    {
        $model = new Model();

        return $model->get_where(
            'post__likes',
            [
                'user_id' => get_userdata('user_id'),
                'post_id' => $post_id,
                'post_path' => $post_path
            ],
            1
        )
        ->num_rows() > 0;
    }
}
