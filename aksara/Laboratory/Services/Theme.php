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

namespace Aksara\Laboratory\Services;

/**
 * Theme service to handle theming-related logic.
 */
class Theme
{
    /**
     * Theme configuration.
     * @var string
     */
    private $_theme = 'default';

    /**
     * View template configuration.
     * @var array
     */
    private $_template = [];

    /**
     * Breadcrumb configuration.
     * @var array
     */
    private $_breadcrumb = [];

    /**
     * Page title configuration.
     * @var array
     */
    private $_title = [];

    /**
     * Fallback page title configuration.
     * @var string
     */
    private $_titleFallback;

    /**
     * Description configuration.
     * @var array
     */
    private $_description = [];

    /**
     * Fallback description configuration.
     * @var string
     */
    private $_descriptionFallback;

    /**
     * Icon configuration.
     * @var array
     */
    private $_icon = [];

    /**
     * Fallback icon configuration.
     * @var string
     */
    private $_iconFallback;

    /**
     * Sets the theme name.
     *
     * @return $this
     */
    public function setTheme(string $theme): static
    {
        $this->_theme = $theme;

        return $this;
    }

    /**
     * Gets the theme name.
     */
    public function get_theme(): string
    {
        return $this->_theme;
    }

    /**
     * Sets custom template properties.
     *
     * @return $this
     */
    public function setTemplate(array|string $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_template = array_merge($this->_template, $params);

        return $this;
    }

    /**
     * Gets template properties.
     */
    public function get_template(): array
    {
        return $this->_template;
    }

    /**
     * Sets custom breadcrumb items.
     *
     * @return $this
     */
    public function setBreadcrumb(array|string $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_breadcrumb = array_merge($this->_breadcrumb, $params);

        return $this;
    }

    /**
     * Gets breadcrumb configuration.
     */
    public function get_breadcrumb(): array
    {
        return $this->_breadcrumb;
    }

    /**
     * Sets the module and document title.
     *
     * @return $this
     */
    public function setTitle(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_title = array_merge($this->_title, $params);
        $this->_titleFallback = $fallback;

        return $this;
    }

    /**
     * Gets title configuration.
     */
    public function get_title(): array
    {
        return $this->_title;
    }

    /**
     * Gets title fallback.
     */
    public function get_title_fallback(): ?string
    {
        return $this->_titleFallback;
    }

    /**
     * Sets the module and meta description.
     *
     * @return $this
     */
    public function setDescription(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_description = array_merge($this->_description, $params);
        $this->_descriptionFallback = $fallback;

        return $this;
    }

    /**
     * Gets description configuration.
     */
    public function get_description(): array
    {
        return $this->_description;
    }

    /**
     * Gets description fallback.
     */
    public function get_description_fallback(): ?string
    {
        return $this->_descriptionFallback;
    }

    /**
     * Sets the content icon.
     *
     * @return $this
     */
    public function setIcon(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_icon = array_merge($this->_icon, $params);
        $this->_iconFallback = $fallback;

        return $this;
    }

    /**
     * Gets icon configuration.
     */
    public function get_icon(): array
    {
        return $this->_icon;
    }

    /**
     * Gets icon fallback.
     */
    public function get_icon_fallback(): ?string
    {
        return $this->_iconFallback;
    }

    /**
     * Gets title by method name or index.
     */
    public function get_title_by_method(string $method): ?string
    {
        return $this->_title[$method] ?? $this->_title['index'] ?? null;
    }

    /**
     * Gets description by method name or index.
     */
    public function get_description_by_method(string $method): ?string
    {
        return $this->_description[$method] ?? $this->_description['index'] ?? null;
    }

    /**
     * Gets icon by method name or index.
     */
    public function get_icon_by_method(string $method): ?string
    {
        return $this->_icon[$method] ?? $this->_icon['index'] ?? null;
    }
}
