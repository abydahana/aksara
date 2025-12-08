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

        // Generate phrases from source code
        $generated_phrases = $this->_generate_phrases_from_source();
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
                    // Failed to read file
                    $error++;
                }
            }

            // Filter: Keep only phrases that are also found in generated phrases
            // $populated_phrases = array_intersect_key($populated_phrases, $generated_phrases);

            // Merge with generated phrases from source code
            $populated_phrases = array_merge($populated_phrases, $generated_phrases);

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

                    // Keep only phrases that are populated
                    $phrases = array_intersect_key($phrases, $populated_phrases);

                    // Merge missing phrases
                    $phrases = array_merge($populated_phrases, $phrases);

                    // Sort phrases by key
                    ksort($phrases);

                    file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $val, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                } catch (\Throwable $e) {
                    // Failed to read or write file
                    $error++;
                }
            }
        }

        if ($error) {
            return throw_exception(403, phrase('Translation synchronized, however there are {{total_errors}} translations were unsuccessful.', ['total_errors' => '<b>' . number_format($error) . '</b>']), current_page('../'));
        }

        return throw_exception(301, phrase('{{total_languages}} languages and {{total_phrases}} phrases was successfully synchronized.', ['total_languages' => '<b>' . number_format(sizeof($languages) - 1) . '</b>', 'total_phrases' => '<b>' . number_format(sizeof($populated_phrases)) . '</b>']), current_page('../'));
    }

    /**
     * Generate phrases from source code by scanning phrase() functions
     */
    private function _generate_phrases_from_source()
    {
        $translations = [];
        $directories = ['aksara', 'modules', 'themes'];
        $fileExtensions = ['php', 'twig'];

        foreach ($directories as $directory) {
            if (! is_dir(ROOTPATH . $directory)) {
                continue;
            }

            $this->_scan_directory(ROOTPATH . $directory, $fileExtensions, $translations);
        }

        return $translations;
    }

    /**
     * Recursively scan directory for PHP and Twig files
     */
    private function _scan_directory($directory, $fileExtensions, &$translations)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), $fileExtensions)) {
                $this->_scan_file($file->getPathname(), $translations);
            }
        }
    }

    /**
     * Scan individual file for phrase() calls
     */
    private function _scan_file($filePath, &$translations)
    {
        $content = file_get_contents($filePath);

        // Remove comments to avoid false positives
        $content = preg_replace('/\/\*.*?\*\//s', '', $content); // Multi-line comments
        $content = preg_replace('/\/\/.*?$/m', '', $content); // Single-line comments

        // Pattern for single quotes: phrase('...')
        if (preg_match_all("/phrase\s*\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->_add_translation($key, $translations, $filePath);
            }
        }

        // Pattern for double quotes: phrase("...")
        if (preg_match_all('/phrase\s*\(\s*"((?:[^"\\\\]|\\\\.)*)"/s', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->_add_translation($key, $translations, $filePath);
            }
        }
    }

    /**
     * Add translation key if valid
     */
    private function _add_translation($key, &$translations, $filePath = '')
    {
        $original_key = $key;

        // Manual unescape - only for escaped quotes
        $key = str_replace("\\'", "'", $key);
        $key = str_replace('\\"', '"', $key);

        // Trim whitespace
        $key = trim($key);

        // Skip empty keys
        if (empty($key)) {
            return;
        }

        // Skip keys with suspicious patterns (likely parsing errors)
        // Allow {{ }} for placeholders, but skip other suspicious patterns
        if (strpos($key, '->') !== false ||
            strpos($key, "('") !== false ||
            strlen($key) > 500 ||
            strpos($key, ';') !== false ||
            preg_match('/\[.*\]/', $key) || // Only skip if has square brackets
            strpos($key, "\n") !== false ||
            strpos($key, "\r") !== false) {
            return;
        }

        // Add to translations array
        if (! isset($translations[$key])) {
            $translations[$key] = $key;
        }
    }
}
