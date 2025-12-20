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

namespace Aksara\Laboratory\Renderer;

use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;
use Throwable;

class Parser
{
    private $_theme;

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
        try {
            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/core')) {
                // Core components not exists, create directory
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/core', 0755, true);
            }

            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/form')) {
                // Form components not exists, create directory
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/form', 0755, true);
            }

            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/table')) {
                // Table components not exists, create directory
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/table', 0755, true);
            }

            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/components/view')) {
                // View components not exists, create directory
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/components/view', 0755, true);
            }

            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/views')) {
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/views', 0755, true);
            }

            // Check components notes existence
            if (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/README')) {
                // Add readme notes
                $notes = <<<EOF
                You can override the template component here;
                Only .twig file are allowed;
                EOF;

                // Create readme file
                file_put_contents(ROOTPATH . 'themes/' . $this->_theme . '/components/README', $notes);
            }

            // Check views path existence
            if (! is_dir(ROOTPATH . 'themes/' . $this->_theme . '/views')) {
                // Create views directory
                mkdir(ROOTPATH . 'themes/' . $this->_theme . '/views', 0755, true);

                // Add readme notes
                $notes = <<<EOF
                You can override the module view here;
                Both .twig or .php file are allowed;
                The view path should be referred to the module structure;
                The i18n view should be placed inside the folder named with language code;
                EOF;

                // Create readme file
                file_put_contents(ROOTPATH . 'themes/' . $this->_theme . '/views/README', $notes);
            }

            if (! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.twig') || ! file_exists(ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.php')) {
                // Copy master views
                copy(APPPATH . 'Views/components/core/404.twig', ROOTPATH . 'themes/' . $this->_theme . '/components/core/404.twig');
            }
        } catch (Throwable $e) {
            // Directory creation failed, stop operation
            exit($e->getMessage());
        }

        // Search paths
        $searchPaths = [
            ROOTPATH . 'themes/' . $this->_theme . '/components/',
            ROOTPATH . 'themes/' . $this->_theme . '/views/',
            APPPATH . 'Views/components/'
        ];

        // Load search paths to twig loader
        $filesystemLoader = new FilesystemLoader($searchPaths);

        // Load Twig environment
        $twig = new Environment($filesystemLoader, [
            'cache' => WRITEPATH . 'cache/twig',
            'auto_reload' => (ENVIRONMENT === 'development'),
            'debug' => (ENVIRONMENT === 'development')
        ]);

        // Debug extension
        $twig->addExtension(new DebugExtension());

        $twig->addFunction(new TwigFunction('phrase', function ($words) {
            return phrase($words);
        }));

        $twig->addFunction(new TwigFunction('truncate', function ($string, $length = 0, $delimeter = '...') {
            return truncate($string, $length, $delimeter);
        }));

        $replacement = json_decode(json_encode($replacement), true) ?? [];

        try {
            // Check if component is a file path (ends with .twig)
            if (str_ends_with($component, '.twig')) {
                foreach ($searchPaths as $path) {
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
