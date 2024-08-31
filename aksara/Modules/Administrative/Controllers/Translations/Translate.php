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

use Aksara\Laboratory\Template;

class Translate extends \Aksara\Laboratory\Core
{
    private $_table = 'app__languages';
    private $_primary;
    private $_code;
    private $_translation_file;
    private $_total_phrases;
    private $_limit;
    private $_limit_backup;
    private $_offset;

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');
        $this->searchable(false);

        $this->_primary = service('request')->getGet('id');

        if (! $this->_primary) {
            return throw_exception(404, phrase('Please choose the language to translate'), current_page('../'));
        }

        $this->_code = service('request')->getGet('code');
        $this->_translation_file = WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->_code . '.json';
        $this->_total_phrases = 0;
        $this->_limit_backup = 99;
        $this->_limit = (service('request')->getGet('limit') ? service('request')->getGet('limit') : $this->_limit_backup);
        $this->_offset = (service('request')->getGet('per_page') > 1 ? (service('request')->getGet('per_page') * $this->_limit) - $this->_limit : 0);
    }

    public function index()
    {
        $template = new Template('backend');
        $phrases = $this->_phrases();

        $this->set_title(phrase('Translate'))
        ->set_icon('mdi mdi-translate')
        ->set_output([
            'phrases' => $phrases,
            'total_phrases' => $this->_total_phrases,
            'pagination' => $template->pagination(
                [
                    'limit' => $this->_limit_backup,
                    'offset' => $this->_offset,
                    'per_page' => $this->_limit,
                    'total_rows' => $this->_total_phrases,
                    'url' => current_page(null, ['per_page' => null])
                ],
                ($this->api_client || service('request')->isAJAX())
            )
        ])
        ->form_callback('validate_translation')
        ->where([
            'id' => $this->_primary
        ])
        ->limit(1)
        ->offset(0)

        ->render($this->_table);
    }

    public function delete_phrase()
    {
        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode'), current_page());
        }

        $delete_key = service('request')->getGet('phrase');

        helper('filesystem');

        $languages = get_filenames(WRITEPATH . 'translations');
        $error = 0;

        if ($languages) {
            foreach ($languages as $key => $val) {
                if (strtolower(pathinfo($val, PATHINFO_EXTENSION)) != 'json') {
                    // Not a translation file
                    continue;
                }

                try {
                    // Attempt to get the translation source
                    $translation = file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val);
                    $phrases = json_decode($translation, true);

                    // Delete phrase
                    unset($phrases[$delete_key]);

                    // Sort and humanize the order of phrase
                    ksort($phrases);

                    // Attempt to update the translation file
                    file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                } catch (\Throwable $e) {
                    // Failed to write file, increase error counts
                    $error++;
                }
            }
        }

        if ($error) {
            return throw_exception(403, phrase('Unable to delete the phrase due the translation path is not writable.'), current_page('../', ['phrase' => null]));
        }

        return throw_exception(301, phrase('The selected phrase was successfully removed.'), current_page('../', ['phrase' => null]));
    }

    public function validate_translation()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page());
        }

        if (file_exists($this->_translation_file)) {
            try {
                $translation = file_get_contents($this->_translation_file);
                $phrases = json_decode($translation, true);

                foreach (service('request')->getPost('phrases') as $key => $val) {
                    if (isset($phrases[$key])) {
                        $phrases[$key] = $val;
                    }
                }

                file_put_contents($this->_translation_file, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                return throw_exception(301, phrase('Data was successfully submitted.'), current_page());
            } catch (\Throwable $e) {
                return throw_exception(403, $e->getMessage(), current_page());
            }
        } else {
            return throw_exception(404, phrase('No language file were found.'), current_page());
        }
    }

    private function _phrases()
    {
        $phrases = [];

        // Check if translation file is exists
        if (file_exists($this->_translation_file)) {
            $translation = file_get_contents($this->_translation_file);
            $phrases = json_decode($translation, true);

            if ($phrases) {
                foreach ($phrases as $key => $val) {
                    if (service('request')->getGet('q') && stripos($val, service('request')->getGet('q')) === false) {
                        // Unset unmatched phrase
                        unset($phrases[$key]);
                    } else {
                        // Escape translation
                        $phrases[$key] = htmlspecialchars($val);
                    }
                }
            }

            // Update phrase total
            $this->_total_phrases = sizeof($phrases);

            // Slice array
            $phrases = array_slice($phrases, $this->_offset, $this->_limit);
        }

        return $phrases;
    }
}
