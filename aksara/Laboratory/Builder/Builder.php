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

namespace Aksara\Laboratory\Builder;

use Throwable;
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
     * Ensure the theme has the full component scaffold required by the renderer.
     */
    public function ensureThemeComponents(string $theme): bool
    {
        if (! $theme) {
            return false;
        }

        foreach (['core', 'table', 'form', 'view'] as $path) {
            if (false === $this->getComponent($theme, $path)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get or create a component template file.
     *
     * This method checks if a specific Twig template exists in the theme directory.
     * If not, it instantiates the relevant Component class, generates the raw HTML/Twig,
     * and writes it to the file system (Auto-Discovery/Auto-Creation).
     */
    public function getComponent(string $theme, string $path, ?string $type = null): string|bool
    {
        static $cache = [];

        $cacheKey = $theme . '|' . $path . '|' . ($type ?? '');
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $component = null;

        try {
            // Set working directory path for the theme components
            $directory = ROOTPATH . "themes/$theme/components";

            // Instantiate the appropriate Builder Class based on path
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

            // Get all available methods (templates) from the builder class
            $templates = get_class_methods($builder);

            // Validate requested type
            // If requested type is invalid or missing, fallback to a default.
            if ($type && ! in_array($type, $templates)) {
                // Fallback logic: 'index' for Core, 'text' for others
                $type = ('core' === $path ? 'index' : 'text');
            }

            $target_dir = $directory . ($path ? DIRECTORY_SEPARATOR . $path : '');
            $requested_file = ($type ? $target_dir . DIRECTORY_SEPARATOR . $type . '.twig' : null);

            // Scaffold components README if missing
            if ($theme && ! file_exists($directory . DIRECTORY_SEPARATOR . 'README')) {
                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $notes = <<<EOF
                You can override the template component here;
                Only .twig file are allowed;
                EOF;

                file_put_contents($directory . DIRECTORY_SEPARATOR . 'README', $notes);
            }

            // Scaffold missing files only if target directory or requested file is missing
            if ($theme) {
                foreach ($templates as $template) {
                    // Skip constructor
                    if ('__construct' === $template) {
                        continue;
                    }

                    // Generate component data
                    $component_data = $builder->$template();
                    $file_to_check = $target_dir . DIRECTORY_SEPARATOR . $component_data['type'] . '.twig';

                    // Check if file exists
                    if (! file_exists($file_to_check)) {
                        // Create directory if it doesn't exist
                        if (! is_dir($target_dir)) {
                            mkdir($target_dir, 0755, true);
                        }

                        // Write the raw component content to the Twig file
                        file_put_contents($file_to_check, $component_data['component']);
                    }
                }
            }

            // Return the requested component content
            if ($requested_file && file_exists($requested_file)) {
                $component = file_get_contents($requested_file);
            }
        } catch (Throwable $e) {
            // Log error or handle gracefully instead of exiting
            log_message('error', '[COMPONENT] ' . $e->getMessage());

            $cache[$cacheKey] = false;

            return false;
        }

        $cache[$cacheKey] = (string) $component;

        return $cache[$cacheKey];
    }
}
