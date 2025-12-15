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

namespace Aksara\Laboratory\Builder;

use Aksara\Laboratory\Builder\Components\Core;
use Aksara\Laboratory\Builder\Components\Table;
use Aksara\Laboratory\Builder\Components\Form;
use Aksara\Laboratory\Builder\Components\View;

/**
 * UI Component Builder Class
 *
 * This class is responsible for generating (scaffolding) Twig template files
 * for the active theme if they do not exist. It acts as a bridge between
 * the raw component templates and the physical file system.
 */
class Builder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // No initialization required
    }

    /**
     * Get or create a component template file.
     *
     * This method checks if a specific Twig template exists in the theme directory.
     * If not, it instantiates the relevant Component class, generates the raw HTML/Twig,
     * and writes it to the file system (Auto-Discovery/Auto-Creation).
     *
     * @param   string      $theme The active theme folder name
     * @param   string      $path  The component category ('core', 'table', 'form', 'view')
     * @param   string|null $type  The specific component method name (e.g., 'text', 'index')
     * @return  string|bool Returns the file content string or false on failure
     */
    public function get_component(string $theme, string $path, ?string $type = null): string|bool
    {
        $component = null;

        try {
            // 1. Set working directory path for the theme components
            $directory = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'components';

            // 2. Instantiate the appropriate Builder Class based on path
            switch ($path) {
                case 'core':
                    $builder = new Core();
                    break;
                case 'table':
                    $builder = new Table();
                    break;
                case 'form':
                    $builder = new Form();
                    break;
                case 'view':
                    $builder = new View();
                    break;
                default:
                    // Invalid path, return false immediately
                    return false;
            }

            // 3. Get all available methods (templates) from the builder class
            $templates = get_class_methods($builder);

            // 4. Validate requested type
            // If requested type is invalid or missing, fallback to a default.
            if ($type && ! in_array($type, $templates)) {
                // Fallback logic: 'index' for Core, 'text' for others
                $type = ('core' === $path ? 'index' : 'text');
            }

            // 5. Scaffold missing files
            // Loop through ALL available methods in the class to ensure all templates exist
            foreach ($templates as $template) {
                // Skip constructor
                if ('__construct' === $template) {
                    continue;
                }

                // Generate component data
                // Note: We do not pass arguments here as the Component classes define parameterless methods
                $component_data = $builder->$template();

                // Define target file path
                $target_dir = $directory . ($path ? DIRECTORY_SEPARATOR . $path : '');
                $target_file = $target_dir . DIRECTORY_SEPARATOR . $component_data['type'] . '.twig';

                // Check if file exists
                if (! file_exists($target_file)) {
                    // Create directory if it doesn't exist
                    if (! is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }

                    // Write the raw component content to the Twig file
                    file_put_contents($target_file, $component_data['component']);
                }
            }

            // 6. Return the requested component content
            if ($type) {
                $requested_file = $directory . ($path ? DIRECTORY_SEPARATOR . $path : '') . DIRECTORY_SEPARATOR . $type . '.twig';

                if (file_exists($requested_file)) {
                    $component = file_get_contents($requested_file);
                }
            }
        } catch (Throwable $e) {
            // Log error or handle gracefully instead of exiting
            // exit($e->getMessage()); // Avoid using exit() in libraries
            return false;
        }

        return (string) $component;
    }
}
