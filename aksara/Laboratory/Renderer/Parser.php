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

namespace Aksara\Laboratory\Renderer;

use Throwable;
use Aksara\Laboratory\Builder\Builder;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class Parser
{
    private string $_theme;
    private static array $_initializedThemes = [];
    private static ?Environment $_twig = null;
    private static string $_twigTheme = '';
    private static array $_searchPaths = [];

    public function __construct()
    {
        $this->_theme = get_theme();
    }

    /**
     * Parse component with replacement
     *
     * @param   object|array $replacement
     */
    public function parse(string $component, $replacement = []): string
    {
        if ($this->_theme && empty(self::$_initializedThemes[$this->_theme])) {
            self::$_initializedThemes[$this->_theme] = true;

            try {
                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/views')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/views', 0755, true);
                }

                (new Builder())->ensureThemeComponents($this->_theme);

                // Check views path existence
                if (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/views/README')) {
                    $notes = <<<EOF
                    You can override the module view here;
                    Both .twig or .php file are allowed;
                    The view path should be referred to the module structure;
                    The i18n view should be placed inside the folder named with language code;
                    EOF;

                    file_put_contents(ROOTPATH . 'themes/' . $this->_theme . '/views/README', $notes);
                }
            } catch (Throwable $e) {
                exit($e->getMessage());
            }
        }

        // Initialize or reuse Twig environment for current theme
        if (null === self::$_twig || self::$_twigTheme !== $this->_theme) {
            self::$_twigTheme = $this->_theme;

            $searchPaths = [];

            if ($this->_theme) {
                $searchPaths[] = ROOTPATH . 'themes/' . $this->_theme . '/components/';
                $searchPaths[] = ROOTPATH . 'themes/' . $this->_theme . '/views/';
            }

            self::$_searchPaths = $searchPaths;

            $filesystemLoader = new FilesystemLoader($searchPaths);

            self::$_twig = new Environment($filesystemLoader, [
                'cache' => WRITEPATH . 'cache/twig',
                'auto_reload' => (ENVIRONMENT === 'development'),
                'debug' => (ENVIRONMENT === 'development')
            ]);

            self::$_twig->addExtension(new DebugExtension());

            self::$_twig->addFunction(new TwigFunction('base_url', function ($slug = null, $query = []) {
                return base_url($slug, $query);
            }));

            self::$_twig->addFunction(new TwigFunction('current_page', function ($slug = null, $query = [], $unset = null) {
                return current_page($slug, $query, $unset);
            }));

            self::$_twig->addFunction(new TwigFunction('phrase', function ($words = null) {
                return phrase($words);
            }));

            self::$_twig->addFunction(new TwigFunction('truncate', function ($string = '', $length = 0, $delimeter = '...') {
                return truncate($string, $length, $delimeter);
            }));
        }

        $twig = self::$_twig;
        $replacement = json_decode(json_encode($replacement), true) ?? [];

        try {
            // Check if component is a file path (ends with .twig)
            if (str_ends_with($component, '.twig')) {
                foreach (self::$_searchPaths as $path) {
                    // If the component starts with one of base paths, strip it
                    if (strpos($component, $path) === 0) {
                        $component = str_replace($path, '', $component);
                        break;
                    }
                }

                return $twig->render($component, $replacement);
            }

            // Otherwise, treat component as a template string
            return $twig->createTemplate($component)->render($replacement);
        } catch (Throwable $e) {
            // Log error and return message instead of killing the script
            if (ENVIRONMENT === 'development') {
                return '<div style="color:red; border:1px solid red; padding:1rem;">Twig Error: ' . $e->getMessage() . '</div>';
            }

            return '';
        }
    }
}
