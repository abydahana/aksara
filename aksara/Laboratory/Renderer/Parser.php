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

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class Parser
{
    private $_theme;

    public function __construct($theme = null)
    {
        $this->_theme = $theme;
    }

    /**
     * Parse component with replacement
     *
     * @param   object|array $replacement
     */
    public function parse(string $component, $replacement = [])
    {
        try {
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
        } catch (\Throwable $e) {
            // Directory creation failed, stop operation
            exit($e->getMessage());
        }

        // Load Twig environment
        $twig = new Environment(new FilesystemLoader([ROOTPATH . 'themes/' . $this->_theme . '/components', ROOTPATH . 'themes/' . $this->_theme . '/views']), [
            'cache' => WRITEPATH . 'cache/twig',
            'auto_reload' => true,
            'debug' => true
        ]);

        // Debug extension
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $twig->addFunction(new TwigFunction('phrase', function ($words) {
            return phrase($words);
        }));

        $twig->addFunction(new TwigFunction('truncate', function ($string, $length = 0, $delimeter = '...') {
            return truncate($string, $length, $delimeter);
        }));

        // Convert replacement object into array
        $replacement = json_decode(json_encode($replacement), true);

        // Default output
        $output = null;

        try {
            // Attempt to get the template component
            $output = $twig->render($component, $replacement);
        } catch(\Throwable $e) {
            // Safe abstraction
        }

        if (! $output) {
            if (file_exists($component) && strtolower(pathinfo($component, PATHINFO_EXTENSION)) === 'twig') {
                // Twig file exists, load component file as string
                try {
                    // Attempt to get the template component
                    $component = file_get_contents($component);
                } catch(\Throwable $e) {
                    // Fail to load component file into string
                    exit($e->getMessage());
                }
            }

            // Create temporary template from component string
            $template = $twig->createTemplate($component);

            // Set new output
            $output = $template->render($replacement);
        }

        // Return rendered template component
        return $output;
    }
}
