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

if (! function_exists('aksara')) {
    /**
     * Get Aksara variable
     */
    function aksara(string $parameter = null)
    {
        $version = '5.1.6';

        if ('version' == $parameter) {
            return $version;
        } elseif ('build_version' == $parameter) {
            return $version . \CodeIgniter\CodeIgniter::CI_VERSION;
        } elseif ('date_modified' == $parameter) {
            $modified = filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php');

            return date('F d Y H:i:s', $modified);
        }

        return false;
    }
}

if (! function_exists('get_setting')) {
    /**
     * Get setting variable
     */
    function get_setting(string $parameter = null)
    {
        $model = new \Aksara\Laboratory\Model();

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

        return false;
    }
}

if (! function_exists('get_userdata')) {
    /**
     * Get user session
     */
    function get_userdata(string $field = '')
    {
        if (! service('session')->get($field) && service('session')->get('user_id')) {
            $model = new \Aksara\Laboratory\Model();
            $user_id = service('session')->get('user_id');

            if ($model->field_exists($field, 'app__users_privileges')) {
                return $model->select($field)->get_where(
                    'app__users_privileges',
                    [
                        'user_id' => $user_id
                    ],
                    1
                )
                ->row($field);
            } elseif ($model->field_exists($field, 'app__users')) {
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

            return false;
        }

        return service('session')->get($field);
    }
}

if (! function_exists('set_userdata')) {
    /**
     * Set user data
     *
     * @param   mixed|null $value
     */
    function set_userdata($key = [], $value = null)
    {
        if (! is_array($key)) {
            $key = [
                $key => $value
            ];
        }

        return service('session')->set($key);
    }
}

if (! function_exists('unset_userdata')) {
    /**
     * Unset user data
     */
    function unset_userdata($key = [])
    {
        return service('session')->remove($key);
    }
}

if (! function_exists('phrase')) {
    /**
     * Get phrase of translation
     */
    function phrase(string $phrase = null, array $replacement = [])
    {
        // Make sure the phrase and language is valid
        if (! $phrase || is_numeric($phrase)) {
            // Otherwise, throwback the null result
            return false;
        }

        // Load model
        $model = new \Aksara\Laboratory\Model();

        // Transform the phrase into safe-string
        $phrase = preg_replace('/[^\w\s\p{P}\p{L}]/u', ' ', $phrase);

        // Remove multiple whitespace
        $phrase = preg_replace('/\s+/', ' ', $phrase);

        // Replace sequare braces to parentheses
        $phrase = str_replace(['[', ']'], ['(', ')'], $phrase);

        // Get locale by session
        $language = get_userdata('language');

        // Check if language session isn't available
        if (! $language) {
            $language_id = (get_userdata('language_id') ? get_userdata('language_id') : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));

            $language = $model->select('
                code
            ')
            ->get_where(
                'app__languages',
                [
                    'id' => $language_id
                ]
            )
            ->row('code');
        }

        $translation_file = WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $language . '.json';

        if (! file_exists($translation_file)) {
            // Translation file not exists
            if (! is_dir(WRITEPATH . 'translations')) {
                // Translation directory not exists
                try {
                    // Try to create directory
                    mkdir(WRITEPATH . 'translations', 0755, true);

                    // Put default content to file
                    file_put_contents($translation_file, json_encode([]));
                } catch (\Throwable $e) {
                    // Safe absraction
                }
            } elseif (is_writable(WRITEPATH . 'translations')) {
                // Translation directory is exists
                try {
                    // Put content into file
                    file_put_contents($translation_file, json_encode([]));
                } catch (\Throwable $e) {
                    // Safe abstraction
                }
            }
        }

        try {
            // Get existing translation
            $buffer = file_get_contents($translation_file);

            // Decode json from translation
            $phrases = (is_json($buffer) ? json_decode($buffer, true) : []);

            // Check if language property is valid
            if (! isset($phrases[$phrase])) {
                // Set new phrase and push into existing
                $phrases[$phrase] = $phrase;

                // Sort and humanize the order of phrase
                ksort($phrases);

                // Put new phrase into existing language
                if (file_exists($translation_file) && is_writable($translation_file)) {
                    file_put_contents($translation_file, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                }
            }

            $phrase = $phrases[$phrase];
        } catch (\Throwable $e) {
            // Safe abstraction
        }

        if ($replacement) {
            // Find and replace
            foreach ($replacement as $keyword => $replace) {
                // Replace string between double braces
                $phrase = preg_replace("/\{\{(\s+)?($keyword)(\s+)?\}\}/", $replace, $phrase);
            }
        }

        return $phrase;
    }
}

if (! function_exists('is_liked')) {
    /**
     * Get if post is liked
     */
    function is_liked(int $post_id = 0, string $post_path = null)
    {
        // Load model
        $model = new \Aksara\Laboratory\Model();

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
