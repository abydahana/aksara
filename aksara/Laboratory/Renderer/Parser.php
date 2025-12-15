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

/**
 * Twig Template Parser
 *
 * This class initializes the Twig environment and handles the parsing (rendering)
 * of components and views, including dynamic directory creation for themes.
 */
class Parser
{
    /**
     * Active theme directory name.
     */
    private string $_theme;

    /**
     * Constructor
     *
     * @param   string|null $theme The theme directory name
     */
    public function __construct(?string $theme = null)
    {
        // Theme name is mandatory for file loading
        $this->_theme = $theme ?? 'default';
    }

    /**
     * Parse component with replacement
     *
     * @param   string $component   The template name (e.g., 'core/toolbar.twig') or raw string content
     * @param   object|array $replacement Associative array or object for variable replacement
     * @return  string Returns the rendered HTML content
     */
    public function parse(string $component, object|array $replacement = []): string
    {
        // 1. Initialize Theme Files
        try {
            $this->_initialize_theme_files();
        } catch (\RuntimeException $e) {
            // Handle error gracefully if initialization failed
            // Returning an empty string prevents application crash (similar to original intent of exit())
            // In a production environment, you might log this error.
            return '';
        }

        $theme_path = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_theme;
        $component_path = $theme_path . DIRECTORY_SEPARATOR . 'components';
        $views_path = $theme_path . DIRECTORY_SEPARATOR . 'views';

        // 2. Setup Twig Environment
        $twig = $this->_setup_twig_environment($component_path, $views_path);

        // Convert replacement object/array into array for Twig consumption
        $replacement = (array) json_decode(json_encode($replacement), true);

        $output = '';

        // 3. Attempt to render template by name
        try {
            // This is the standard way to render a template file (e.g., 'core/toolbar.twig')
            $output = $twig->render($component, $replacement);
        } catch (\Twig\Error\LoaderError $e) {
            // LoaderError: Template file not found, implies $component is a raw string, proceed to create template
        } catch (Throwable $e) {
            // Other Twig Errors (syntax, etc.), keep output empty
        }

        // 4. Fallback: If template rendering failed, assume $component is raw string content
        if (empty($output)) {
            $raw_content = $component;

            // If the component string is actually a file path (for fallback loading)
            if (file_exists($component) && strtolower(pathinfo($component, PATHINFO_EXTENSION)) === 'twig') {
                try {
                    $raw_content = file_get_contents($component);
                } catch (Throwable $e) {
                    // Failed to load file content, return empty string instead of exit()
                    return '';
                }
            }

            try {
                // Create temporary template from component string
                $template = $twig->createTemplate($raw_content);

                // Render content
                $output = $template->render($replacement);
            } catch (Throwable $e) {
                // Final safe abstraction if parsing raw content fails
            }
        }

        return $output;
    }

    /**
     * Initializes theme directories, files, and Twig environment setup.
     *
     * @throws \RuntimeException If directory creation fails.
     */
    private function _initialize_theme_files(): void
    {
        $theme_path = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_theme;
        $component_path = $theme_path . DIRECTORY_SEPARATOR . 'components';
        $views_path = $theme_path . DIRECTORY_SEPARATOR . 'views';

        // Required component sub-directories
        $required_dirs = [
            $component_path . DIRECTORY_SEPARATOR . 'core',
            $component_path . DIRECTORY_SEPARATOR . 'form',
            $component_path . DIRECTORY_SEPARATOR . 'table',
            $component_path . DIRECTORY_SEPARATOR . 'view',
            $views_path,
        ];

        // 1. Create Directories
        foreach ($required_dirs as $dir) {
            if (! is_dir($dir)) {
                if (! mkdir($dir, 0755, true)) {
                    throw new \RuntimeException("Failed to create theme directory: " . $dir);
                }
            }
        }

        // 2. Create README notes for components
        $readme_component_file = $component_path . DIRECTORY_SEPARATOR . 'README';
        if (! file_exists($readme_component_file)) {
            $notes = <<<EOF
            You can override the template component here;
            Only .twig file are allowed;
            EOF;
            file_put_contents($readme_component_file, $notes);
        }

        // 3. Create README notes for views
        $readme_views_file = $views_path . DIRECTORY_SEPARATOR . 'README';
        if (! file_exists($readme_views_file)) {
            $notes = <<<EOF
            You can override the module view here;
            Both .twig or .php file are allowed;
            The view path should be referred to the module structure;
            The i18n view should be placed inside the folder named with language code;
            EOF;
            file_put_contents($views_path . DIRECTORY_SEPARATOR . 'README', $notes);
        }

        // 4. Copy 404 master view if missing
        $target_404 = $component_path . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '404.twig';
        if (! file_exists($target_404)) {
            copy(APPPATH . 'Views/components/core/404.twig', $target_404);
        }
    }

    /**
     * Initializes the Twig Environment and registers custom functions.
     *
     * @param   string $component_path Theme component path
     * @param   string $views_path     Theme view path
     */
    private function _setup_twig_environment(string $component_path, string $views_path): Environment
    {
        // Loaders: prioritize components then views
        $loader = new FilesystemLoader([$component_path, $views_path]);

        // Environment configuration
        $twig = new Environment($loader, [
            'cache' => WRITEPATH . 'cache/twig',
            'auto_reload' => true,
            'debug' => true
        ]);

        // Debug extension
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        // Register custom helper functions
        $twig->addFunction(new TwigFunction('phrase', function (string $words): string {
            return phrase($words);
        }));

        $twig->addFunction(new TwigFunction('truncate', function (string $string, int $length = 0, string $delimeter = '...'): string {
            return truncate($string, $length, $delimeter);
        }));

        return $twig;
    }
}
