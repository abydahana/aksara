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
 * When the signs come, those who do not believe at that time
 * will have only two choices: commit suicide or become brutal.
 */

namespace Aksara\Libraries\PageBuilder;

use Config\PageBuilder as PageBuilderConfig;

/**
 * Page Builder — Main Orchestrator.
 *
 * Provides the high-level API for the page builder system:
 *  - Component registry access
 *  - JSON layout rendering to HTML
 *  - Template management
 */
class PageBuilder
{
    private PageBuilderConfig $config;
    private Renderer $renderer;

    public function __construct()
    {
        $this->config = config('PageBuilder');
        $this->renderer = new Renderer($this->config);
    }

    /**
     * Render a JSON layout array to HTML.
     *
     * @param array $layout The page_layout JSON decoded to array.
     *
     * @return string Rendered HTML.
     */
    public function render(array $layout): string
    {
        return $this->renderer->render($layout);
    }

    /**
     * Get all registered components grouped by category.
     *
     * @return array<string, array> Components keyed by category.
     */
    public function getComponents(): array
    {
        $grouped = [];

        foreach ($this->config->components as $type => $definition) {
            $category = $definition['category'] ?? 'layout';
            $grouped[$category][$type] = $definition;
        }

        return $grouped;
    }

    /**
     * Get component categories with labels.
     *
     * @return array<string, string>
     */
    public function getCategories(): array
    {
        return $this->config->componentCategories;
    }

    /**
     * Get a flat list of all components (for JS consumption).
     *
     * @return array<string, array>
     */
    public function getComponentsFlat(): array
    {
        return $this->config->components;
    }

    /**
     * Get available templates.
     *
     * @return array<string, array>
     */
    public function getTemplates(): array
    {
        return $this->config->templates;
    }

    /**
     * Get a specific template layout.
     *
     * @param string $key Template key.
     *
     * @return array Layout array.
     */
    public function getTemplate(string $key): array
    {
        return $this->config->getTemplate($key);
    }

    /**
     * Get the active framework class mappings.
     *
     * @return array<string, mixed>
     */
    public function getFrameworkClasses(): array
    {
        $fw = $this->config->framework;

        return $this->config->frameworks[$fw] ?? $this->config->frameworks['bootstrap5'];
    }

    /**
     * Validate a layout JSON structure.
     *
     * @param array $layout The layout to validate.
     *
     * @return array{valid: bool, errors: list<string>}
     */
    public function validate(array $layout): array
    {
        $errors = [];

        if (! isset($layout['version'])) {
            $errors[] = 'Missing "version" field.';
        }

        if (! isset($layout['components']) || ! is_array($layout['components'])) {
            $errors[] = 'Missing or invalid "components" field.';
        } else {
            foreach ($layout['components'] as $index => $component) {
                if (! isset($component['type'])) {
                    $errors[] = "Component at index {$index} missing \"type\" field.";
                } elseif (! isset($this->config->components[$component['type']])) {
                    $errors[] = "Unknown component type \"{$component['type']}\" at index {$index}.";
                }
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }
}
