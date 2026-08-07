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
        if ($this->_theme && (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/README') || empty(self::$_initializedThemes[$this->_theme]))) {
            self::$_initializedThemes[$this->_theme] = true;

            try {
                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/core')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/core', 0755, true);
                }

                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/form')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/form', 0755, true);
                }

                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/table')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/table', 0755, true);
                }

                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/view')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/view', 0755, true);
                }

                if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/views')) {
                    mkdir(ROOTPATH . 'themes/' . $this->_theme . '/views', 0755, true);
                }

                // Check components notes existence
                if (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/README')) {
                    $notes = <<<EOF
                    You can override the template component here;
                    Only .twig file are allowed;
                    EOF;

                    file_put_contents(ROOTPATH . 'themes/' . $this->_theme . '/components/README', $notes);
                }

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

                if (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.twig') && ! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.php')) {
                    $template = <<<EOF
                    <div class="container pt-5 pb-5">
                        <div class="text-center pt-5 pb-5">
                            <h1 class="text-muted">
                                404
                            </h1>
                            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6 offset-md-3">
                                <h2 class="text-center">
                                    {{ phrase('Page not found!') }}
                                </h2>
                                <p class="fs-5 text-center mb-5">
                                    {{ phrase('The page you requested does not exist or already been archived.') }}
                                </p>
                                <div class="text-center mt-5">
                                    <a href="#" class="btn btn-outline-primary rounded-pill">
                                        <i class="mdi mdi-arrow-left"></i>
                                        {{ phrase('Back to Homepage') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        {% if suggestions %}
                            <div class="row mb-2">
                                <div class="col-md-10 offset-md-1">
                                    <h5>
                                        {{ phrase('Our suggestions') }}
                                        <blink>_</blink>
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 offset-md-1">
                                    {% for index, page in suggestions %}
                                        {% if index %} &middot; {% endif %}
                                        <a href="{{ links.base_url }}pages/{{ page.page_slug }}" class="--xhr">
                                            {{ page.page_title }}
                                        </a>
                                    {% endfor %}
                                </div>
                            </div>
                        {% endif %}
                    </div>
                    EOF;

                    file_put_contents(ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.twig', $template);
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
