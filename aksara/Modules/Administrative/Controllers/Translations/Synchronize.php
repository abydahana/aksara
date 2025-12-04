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

namespace Aksara\Modules\Administrative\Controllers\Translations;

class Synchronize extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page('../'));
        }

        $this->set_permission();
        $this->set_theme('backend');
    }

    public function index()
    {
        helper('filesystem');

        $languages = get_filenames(WRITEPATH . 'translations');
        $populated_phrases = [];
        $error = 0;

        if ($languages) {
            // First looping, to populate all phrases
            foreach ($languages as $key => $val) {
                if (strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') {
                    // Not a valid translation file
                    continue;
                }

                try {
                    // Attempt to get the translation source
                    $translation = file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);

                    // Merge phrases
                    $populated_phrases = array_merge($populated_phrases, json_decode($translation, true));
                } catch (\Throwable $e) {
                    /* failed to write file, throw an error exception */
                    $error++;
                }
            }

            // Combine array value using its key
            $populated_phrases = array_combine(array_keys($populated_phrases), array_keys($populated_phrases));

            // Second looping, to assign populated phrases into translation
            foreach ($languages as $key => $val) {
                if (strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') {
                    // Not a valid translation file
                    continue;
                }

                try {
                    // Attempt to get the translation source
                    $translation = file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
                    $phrases = json_decode($translation, true);
                    $phrases = array_merge($populated_phrases, $phrases);

                    /* sort and humanize the order of phrase */
                    ksort($phrases);

                    file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                } catch (\Throwable $e) {
                    /* failed to write file, throw an error exception */
                    $error++;
                }
            }
        }

        if ($error) {
            return throw_exception(403, phrase('Translation synchronized!') . ' ' .  phrase('However, there are') . ' <b>' . number_format($error) . '</b> ' . phrase('translations were unsuccessful.'), current_page('../'));
        }

        return throw_exception(301, '<b>' . (sizeof($languages) - 1) . '</b> ' . ((sizeof($languages) - 1) > 1 ? phrase('languages') : phrase('language')) . ' ' . phrase('was successfully updated.') . ' <b>' . number_format(sizeof($populated_phrases)) . '</b> ' . phrase('phrases was synchronized.'), current_page('../'));
    }
}
