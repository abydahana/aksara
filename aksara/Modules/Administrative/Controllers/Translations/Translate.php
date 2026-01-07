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
use Aksara\Laboratory\Core;
use Throwable;

class Translate extends Core
{
    private $_table = 'app__languages';
    private $_primary;
    private $_code;
    private $_translationFile;
    private $_totalPhrases;
    private $_limit;
    private $_limitBackup;
    private $_offset;

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');
        $this->searchable(false);

        $this->_primary = $this->request->getGet('id');

        if (! $this->_primary) {
            return throw_exception(404, phrase('Please choose the language to translate.'), current_page('../'));
        }

        $this->_code = $this->request->getGet('code');
        $this->_translation_file = WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->_code . '.json';
        $this->_total_phrases = 0;
        $this->_limitBackup = 99;
        $this->_limit = ($this->request->getGet('limit') ? $this->request->getGet('limit') : $this->_limitBackup);
        $this->_offset = ($this->request->getGet('per_page') > 1 ? ($this->request->getGet('per_page') * $this->_limit) - $this->_limit : 0);
    }

    public function index()
    {
        $template = new Template('backend');
        $phrases = $this->_phrases();

        $this->setTitle(phrase('Translate'))
        ->setIcon('mdi mdi-translate')
        ->setOutput([
            'phrases' => $phrases,
            'total_phrases' => $this->_total_phrases,
            'pagination' => $template->pagination(
                [
                    'limit' => $this->_limitBackup,
                    'offset' => $this->_offset,
                    'per_page' => $this->_limit,
                    'total_rows' => $this->_total_phrases,
                    'url' => current_page(null, ['per_page' => null])
                ],
                ($this->apiClient || $this->request->isAJAX())
            )
        ])
        ->formCallback('validate_translation')
        ->where([
            'id' => $this->_primary
        ])
        ->limit(1)
        ->offset(0)

        ->render($this->_table);
    }

    public function deletePhrase()
    {
        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page('../'));
        }

        $deleteKey = $this->request->getGet('phrase');

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
                    unset($phrases[$deleteKey]);

                    // Sort and humanize the order of phrase
                    ksort($phrases);

                    // Attempt to update the translation file
                    file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                } catch (Throwable $e) {
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

    public function validateTranslation()
    {
        if (DEMO_MODE) {
            return throw_exception(404, phrase('Changes will not saved in demo mode.'), current_page());
        }

        if (file_exists($this->_translation_file)) {
            try {
                // Set internal encoding to UTF-8
                mb_internal_encoding('UTF-8');

                $translation = file_get_contents($this->_translation_file);
                $phrases = json_decode($translation, true);
                $phrasesInput = $this->request->getPost('phrases');

                if (! is_array($phrases)) {
                    $phrases = [];
                }

                if (! is_array($phrasesInput)) {
                    $phrasesInput = [];
                }

                foreach ($phrasesInput as $key => $val) {
                    if (isset($phrases[$key])) {
                        if ($val) {
                            // Sanitize unsafe input - allow safe formatting tags
                            $val = strip_tags($val, '<a><b><i><u><strong><em><small><br><span>');

                            // Remove all event handlers and dangerous attributes
                            $val = preg_replace('/<([^>]+)\s+(on\w+)=["\']?[^"\']*["\']?([^>]*)>/i', '<$1$3>', $val);

                            // Sanitize <a> tags - only allow href, title, target
                            $val = preg_replace_callback(
                                '/<a\s+([^>]+)>/i',
                                function ($matches) {
                                    $attrs = $matches[1];
                                    $allowedAttrs = [];

                                    // Extract href
                                    if (preg_match('/href=["\']([^"\']*)["\']/', $attrs, $hrefMatch)) {
                                        $href = $hrefMatch[1];
                                        // Only allow http, https, mailto, and relative URLs
                                        if (preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $href)) {
                                            $allowedAttrs[] = 'href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"';
                                        }
                                    }

                                    // Extract title
                                    if (preg_match('/title=["\']([^"\']*)["\']/', $attrs, $titleMatch)) {
                                        $allowedAttrs[] = 'title="' . htmlspecialchars($titleMatch[1], ENT_QUOTES, 'UTF-8') . '"';
                                    }

                                    // Extract target (only _blank or _self)
                                    if (preg_match('/target=["\'](_blank|_self)["\']/', $attrs, $targetMatch)) {
                                        $allowedAttrs[] = 'target="' . $targetMatch[1] . '"';
                                        // Add rel="noopener noreferrer" for security if target is _blank
                                        if ('_blank' === $targetMatch[1]) {
                                            $allowedAttrs[] = 'rel="noopener noreferrer"';
                                        }
                                    }

                                    return '<a ' . implode(' ', $allowedAttrs) . '>';
                                },
                                $val
                            );

                            // Sanitize <span> tags - only allow class and style with limited properties
                            $val = preg_replace_callback(
                                '/<span\s+([^>]+)>/i',
                                function ($matches) {
                                    $attrs = $matches[1];
                                    $allowedAttrs = [];

                                    // Extract class (alphanumeric, dash, underscore only)
                                    if (preg_match('/class=["\']([a-zA-Z0-9\s_-]+)["\']/', $attrs, $classMatch)) {
                                        $allowedAttrs[] = 'class="' . htmlspecialchars($classMatch[1], ENT_QUOTES, 'UTF-8') . '"';
                                    }

                                    // Extract style (only color and font-weight)
                                    if (preg_match('/style=["\']([^"\']*)["\']/', $attrs, $styleMatch)) {
                                        $style = $styleMatch[1];
                                        $safeStyles = [];

                                        // Allow color
                                        if (preg_match('/color:\s*([#a-zA-Z0-9]+)/', $style, $colorMatch)) {
                                            $safeStyles[] = 'color: ' . $colorMatch[1];
                                        }

                                        // Allow font-weight
                                        if (preg_match('/font-weight:\s*(bold|normal|[1-9]00)/', $style, $weightMatch)) {
                                            $safeStyles[] = 'font-weight: ' . $weightMatch[1];
                                        }

                                        if ($safeStyles) {
                                            $allowedAttrs[] = 'style="' . implode('; ', $safeStyles) . '"';
                                        }
                                    }

                                    return $allowedAttrs ? '<span ' . implode(' ', $allowedAttrs) . '>' : '<span>';
                                },
                                $val
                            );

                            // Remove any remaining javascript: or data: protocols
                            $val = preg_replace('/(<[^>]+)(javascript:|data:)/i', '$1blocked:', $val);

                            // Ensure UTF-8 encoding
                            $val = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
                        }

                        $phrases[$key] = $val;
                    }
                }

                // Save with proper UTF-8 encoding flags
                $jsonContent = json_encode(
                    $phrases,
                    JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_SLASHES |
                    JSON_UNESCAPED_UNICODE
                );

                // Write with file lock to prevent race condition
                file_put_contents($this->_translation_file, $jsonContent, LOCK_EX);

                return throw_exception(301, phrase('Data was successfully submitted.'), current_page());
            } catch (Throwable $e) {
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
                    if ($this->request->getGet('q') && stripos($val, $this->request->getGet('q')) === false) {
                        // Unset unmatched phrase
                        unset($phrases[$key]);
                    } else {
                        // Escape translation
                        $phrases[$key] = htmlspecialchars($val);
                    }
                }

                // Separate identical and non-identical key-value pairs
                $identicalPairs = [];
                $nonIdenticalPairs = [];

                foreach ($phrases as $key => $val) {
                    if ($key === $val) {
                        $identicalPairs[$key] = $val;
                    } else {
                        $nonIdenticalPairs[$key] = $val;
                    }
                }

                // Sort by untranslated first
                $phrases = array_merge($identicalPairs, $nonIdenticalPairs);
            }

            // Update phrase total
            $this->_total_phrases = sizeof($phrases);

            // Slice array
            $phrases = array_slice($phrases, $this->_offset, $this->_limit);
        }

        return $phrases;
    }
}
