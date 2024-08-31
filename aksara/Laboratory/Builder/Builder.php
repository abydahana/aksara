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

class Builder
{
    public function __construct()
    {
        // Safe abstraction
    }

    public function get_component(string $theme, string $path, $type = null)
    {
        $component = null;

        try {
            // Set working path
            $directory = ROOTPATH . 'themes/' . $theme . '/components';

            if ('core' === $path) {
                // Load form component
                $builder = new Core();
            } elseif ('table' === $path) {
                // Load form component
                $builder = new Table();
            } elseif ('form' === $path) {
                // Load form component
                $builder = new Form();
            } elseif ('view' === $path) {
                // Load form component
                $builder = new View();
            }

            // List available templates from class method
            $templates = get_class_methods($builder);

            if ($type && ! in_array($type, $templates)) {
                $type = 'text';
            }

            foreach ($templates as $key => $template) {
                // Continue on unnecessary method
                if (in_array($template, ['__construct'])) {
                    continue;
                }

                // Get component
                $component = $builder->$template($type);

                // Theme found, now create default component when no file exists
                if (! file_exists($directory . ($path ? '/' . $path : null) . '/' . $component['type'] . '.twig')) {
                    // Component file not exists
                    if (! is_dir($directory . ($path ? '/' . $path : null))) {
                        // Try to create directory
                        mkdir($directory . ($path ? '/' . $path : null), 0755, true);
                    }

                    // Put component to file
                    file_put_contents($directory . ($path ? '/' . $path : null) . '/' . $component['type'] . '.twig', $component['component']);
                }
            }

            if ($type) {
                // Get component
                $component = file_get_contents($directory . ($path ? '/' . $path : null) . '/' . $type . '.twig');
            }
        } catch (\Throwable $e) {
            // Safe abstraction
            exit($e->getMessage());
        }

        return $component;
    }
}
