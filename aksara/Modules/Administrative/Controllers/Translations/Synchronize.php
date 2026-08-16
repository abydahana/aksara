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

namespace Aksara\Modules\Administrative\Controllers\Translations;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use Aksara\Laboratory\Core;
use Aksara\Laboratory\Model;

class Synchronize extends Core
{
    public function __construct()
    {
        parent::__construct();

        if (DEMO_MODE) {
            return throw_exception(403, phrase('Changes will not saved in demo mode.'), current_page('../'));
        }

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        helper('filesystem');

        // Generate phrases from source code
        $generatedScopes = $this->_generatePhrasesFromSource();
        $languages = $this->_languages();
        $error = 0;
        $uniquePhrases = [];
        $existingDocuments = [];

        foreach ($generatedScopes as $generatedPhrases) {
            $uniquePhrases += $generatedPhrases;
        }

        if ($languages) {
            foreach ($languages as $language) {
                $existingDocuments[$language] = $this->_translationDocuments($language);

                foreach ($existingDocuments[$language] as $scope => $existingPhrases) {
                    if (! isset($generatedScopes[$scope])) {
                        $generatedScopes[$scope] = [];
                    }

                    foreach (array_keys($existingPhrases) as $phrase) {
                        $generatedScopes[$scope][$phrase] = $phrase;
                        $uniquePhrases[$phrase] = $phrase;
                    }
                }
            }

            foreach ($languages as $language) {
                $existingScopes = $existingDocuments[$language] ?? [];

                foreach ($generatedScopes as $scope => $generatedPhrases) {
                    try {
                        $file = $this->_translationFile($language, $scope);
                        $existingPhrases = $existingScopes[$scope] ?? [];
                        $phrases = array_combine(array_keys($generatedPhrases), array_keys($generatedPhrases)) ?: [];

                        if (is_array($existingPhrases)) {
                            $phrases = array_merge($phrases, array_intersect_key($existingPhrases, $phrases));
                        }

                        if (! $phrases) {
                            if (file_exists($file)) {
                                unlink($file);
                            }

                            if ('core' !== $scope && is_dir(dirname($file)) && ! (glob(dirname($file) . DIRECTORY_SEPARATOR . '*') ?: [])) {
                                rmdir(dirname($file));
                            }

                            continue;
                        }

                        ksort($phrases);

                        if (! is_dir(dirname($file))) {
                            mkdir(dirname($file), 0755, true);
                        }

                        file_put_contents($file, json_encode($phrases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE));
                        clear_translations_cache($language);
                    } catch (Throwable $e) {
                        $error++;
                    }
                }
            }
        }

        if ($error) {
            return throw_exception(403, phrase('Translation synchronized, however there are {{total_errors}} translations were unsuccessful.', ['total_errors' => '<b>' . number_format($error) . '</b>']), current_page('../'));
        }

        return throw_exception(301, phrase('{{total_languages}} languages and {{total_phrases}} phrases was successfully synchronized.', ['total_languages' => '<b>' . number_format(sizeof($languages)) . '</b>', 'total_phrases' => '<b>' . number_format(sizeof($uniquePhrases)) . '</b>']), current_page('../'));
    }

    private function _languages(): array
    {
        try {
            $model = new Model();
            $languages = $model->select('code')
            ->getWhere('app_languages')
            ->result();

            if ($languages) {
                return array_values(array_filter(array_map(static fn ($language) => $language->code ?? null, $languages)));
            }
        } catch (Throwable $e) {
            log_message('error', '[TRANSLATION] Unable to load language codes: ' . $e->getMessage());
        }

        $languages = [];

        foreach (get_filenames(WRITEPATH . 'translations') ?: [] as $file) {
            if ('json' === strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                $languages[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return array_values(array_unique($languages));
    }

    /**
     * Generate phrases from source code by scanning phrase() functions
     */
    private function _generatePhrasesFromSource()
    {
        $translations = [];
        $directories = ['aksara', 'modules', 'themes', 'public/assets/local/js'];
        $fileExtensions = ['php', 'twig', 'js'];

        foreach ($directories as $directory) {
            if (! is_dir(ROOTPATH . $directory)) {
                continue;
            }

            $this->_scanDirectory(ROOTPATH . $directory, $fileExtensions, $translations);
        }

        return $translations;
    }

    /**
     * Recursively scan directory for translation-aware source files
     */
    private function _scanDirectory(string $directory, array $fileExtensions, array &$translations)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), $fileExtensions)) {
                $this->_scanFile($file->getPathname(), $translations);
            }
        }
    }

    /**
     * Scan individual file for phrase() calls
     */
    private function _scanFile(string $filePath, array &$translations)
    {
        $content = file_get_contents($filePath);

        // Remove comments to avoid false positives
        $content = preg_replace('/\/\*.*?\*\//s', '', $content); // Multi-line comments
        $content = preg_replace('/\/\/.*?$/m', '', $content); // Single-line comments

        // Pattern for single quotes: phrase('...')
        if (preg_match_all("/phrase\s*\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->_addTranslation($key, $translations, $filePath);
            }
        }

        // Pattern for double quotes: phrase("...")
        if (preg_match_all('/phrase\s*\(\s*"((?:[^"\\\\]|\\\\.)*)"/s', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->_addTranslation($key, $translations, $filePath);
            }
        }
    }

    /**
     * Add translation key if valid
     */
    private function _addTranslation(string $key, array &$translations, string $filePath = '')
    {
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

        $scope = $this->_scopeFromPath($filePath);

        if (! isset($translations[$scope])) {
            $translations[$scope] = [];
        }

        // Add to translations array
        if (! isset($translations[$scope][$key])) {
            $translations[$scope][$key] = $key;
        }
    }

    private function _scopeFromPath(string $filePath): string
    {
        $normalized = str_replace('\\', '/', $filePath);
        $root = rtrim(str_replace('\\', '/', ROOTPATH), '/') . '/';

        if (str_starts_with($normalized, $root)) {
            $normalized = substr($normalized, strlen($root));
        }

        if (preg_match('#^modules/([^/]+)/#i', $normalized, $match)) {
            return strtolower($match[1]);
        }

        return 'core';
    }

    private function _translationFile(string $language, string $scope = 'core'): string
    {
        return WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . ('core' === $scope ? '' : $scope . DIRECTORY_SEPARATOR) . $language . '.json';
    }

    private function _translationDocuments(string $language): array
    {
        $documents = [];
        $base = WRITEPATH . 'translations';
        $root = $this->_translationFile($language);

        if (file_exists($root)) {
            $documents['core'] = $this->_readTranslation($root);
        }

        foreach (glob($base . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $language . '.json') ?: [] as $file) {
            $documents[basename(dirname($file))] = $this->_readTranslation($file);
        }

        return $documents;
    }

    private function _readTranslation(string $file): array
    {
        $phrases = json_decode(file_get_contents($file) ?: '[]', true);

        return is_array($phrases) ? $phrases : [];
    }
}
