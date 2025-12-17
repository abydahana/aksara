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

namespace Aksara\Laboratory\Builder\Components;

/**
 * View (Read-Only) Component Builder
 *
 * This class contains raw Twig templates used to render data in the Read/Detail view.
 * Unlike the Form builder, this builder renders static HTML elements (divs, spans, lists)
 * optimized for displaying data, not editing it.
 */
class View
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // No initialization required
    }

    /**
     * Generate Text Component.
     * Renders standard text content wrapped in a div with word-break styling.
     *
     * @return  array Returns component configuration array
     */
    public function text(): array
    {
        // Template for Text Display
        $component = <<<EOF
        <div class="text-break-word">
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Textarea Component.
     * Renders long text content, ensuring it breaks correctly within the container.
     *
     * @return  array Returns component configuration array
     */
    public function textarea(): array
    {
        // Template for Long Text Display
        $component = <<<EOF
        <div class="text-break-word">
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate WYSIWYG Component.
     * Renders rich HTML content.
     *
     * @return  array Returns component configuration array
     */
    public function wysiwyg(): array
    {
        // Template for Rich Text Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Number Component.
     * Renders numeric content.
     *
     * @return  array Returns component configuration array
     */
    public function number(): array
    {
        // Template for Number Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Money Component.
     * Renders currency content.
     *
     * @return  array Returns component configuration array
     */
    public function money(): array
    {
        // Template for Currency Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Percent Component.
     * Renders percentage formatted number.
     *
     * @return  array Returns component configuration array
     */
    public function percent(): array
    {
        // Template for Percentage Display
        $component = <<<EOF
        <div>
            {{ content | format_percent_number }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Select Component.
     * Renders the label of the selected option.
     *
     * @return  array Returns component configuration array
     */
    public function select(): array
    {
        // Template for Select Label Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Checkbox Component.
     * Renders the selected checkbox values.
     *
     * @return  array Returns component configuration array
     */
    public function checkbox(): array
    {
        // Template for Checkbox Values Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Radio Component.
     * Renders the selected radio button value.
     *
     * @return  array Returns component configuration array
     */
    public function radio(): array
    {
        // Template for Radio Value Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Boolean Component.
     * Renders a badge indicating Active/Inactive status.
     *
     * @return  array Returns component configuration array
     */
    public function boolean(): array
    {
        // Template for Status Badge
        $component = <<<EOF
        <div>
            <span class="badge {{ value ? 'bg-success' : 'bg-danger' }}">
                {% if value %}
                    {{ phrase('Active') }}
                {% else %}
                    {{ phrase('Inactive') }}
                {% endif %}
            </span>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Range Component.
     * Renders a disabled range input to visualize value.
     *
     * @return  array Returns component configuration array
     */
    public function range(): array
    {
        // Template for Range Visualization
        $component = <<<EOF
        <div>
            <input type="range" value="{{ value }}" min="0" max="100" disabled>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Color Component.
     * Renders a disabled color input to visualize color.
     *
     * @return  array Returns component configuration array
     */
    public function color(): array
    {
        // Template for Color Sample
        $component = <<<EOF
        <div>
            <input type="color" value="{{ value }}" disabled>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Date Component.
     * Renders a date string.
     *
     * @return  array Returns component configuration array
     */
    public function date(): array
    {
        // Template for Date Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate DateTime Component.
     * Renders a datetime string.
     *
     * @return  array Returns component configuration array
     */
    public function datetime(): array
    {
        // Template for DateTime Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Time Component.
     * Renders a time string.
     *
     * @return  array Returns component configuration array
     */
    public function time(): array
    {
        // Template for Time Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Week Component.
     * Renders a week string.
     *
     * @return  array Returns component configuration array
     */
    public function week(): array
    {
        // Template for Week Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Month Component.
     * Renders a month string.
     *
     * @return  array Returns component configuration array
     */
    public function month(): array
    {
        // Template for Month Display
        $component = <<<EOF
        <div>
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Hidden Component.
     * Renders a placeholder text indicating hidden content.
     *
     * @return  array Returns component configuration array
     */
    public function hidden(): array
    {
        // Template for Hidden Placeholder
        $component = <<<EOF
        <div>
            *{{ phrase('Hidden') }}*
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Email Component.
     * Renders an email address with word-break styling.
     *
     * @return  array Returns component configuration array
     */
    public function email(): array
    {
        // Template for Email Display
        $component = <<<EOF
        <div class="text-break-word">
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Password Component.
     * Renders masked characters.
     *
     * @return  array Returns component configuration array
     */
    public function password(): array
    {
        // Template for Masked Password
        $component = <<<EOF
        <div>
            ******
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Encryption Component.
     * Renders masked characters for encrypted fields.
     *
     * @return  array Returns component configuration array
     */
    public function encryption(): array
    {
        // Template for Masked Encrypted String
        $component = <<<EOF
        <div>
            ******
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate File Component.
     * Renders text representing a file (usually a link generated elsewhere).
     *
     * @return  array Returns component configuration array
     */
    public function file(): array
    {
        // Template for File Link
        $component = <<<EOF
        <div class="text-break-word">
            {{ content | raw }}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Files List Component.
     * Renders an unordered list of file links.
     *
     * @return  array Returns component configuration array
     */
    public function files(): array
    {
        // Template for Multiple Files List
        $component = <<<EOF
        <div>
            <ul>
                {% for file in content %}
                    <li>
                        <a href="{{ file.url }}" target="_blank">
                            {{ file.name }}
                        </a>
                    </li>
                {% endfor %}
            </ul>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Image Component.
     * Renders a single image with a link to the full version.
     *
     * @return  array Returns component configuration array
     */
    public function image(): array
    {
        // Template for Single Image
        $component = <<<EOF
        <div class="text-sm-center">
            <a href="{{ content | replace({'/thumbs/': '/'}) }}" target="_blank">
                <img src="{{ content }}" alt="{{ label }}" class="rounded img-fluid" />
            </a>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Images List Component.
     * Renders a list of links to multiple images.
     *
     * @return  array Returns component configuration array
     */
    public function images(): array
    {
        // Template for Multiple Images List
        $component = <<<EOF
        <div>
            <ul>
                {% for file in content %}
                    <li>
                        <a href="{{ file.url }}" target="_blank">
                            {{ file.name }}
                        </a>
                    </li>
                {% endfor %}
            </ul>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Hyperlink Component.
     * Renders a custom link with an icon.
     *
     * @return  array Returns component configuration array
     */
    public function hyperlink(): array
    {
        // Template for Custom Link
        $component = <<<EOF
        <div>
            <a href="{{ content }}" class="--xhr" target="{{ target }}">
                <b> {{ value }}<i class="mdi mdi-launch"></i> </b>
            </a>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Geospatial Component.
     * Renders a map visualization for geo-coordinates.
     *
     * @return  array Returns component configuration array
     */
    public function geospatial(): array
    {
        // Template for Map Visualization
        $component = <<<EOF
        <div class="drawing-placeholder preloader position-relative w-100 overflow-hidden">
            <div role="map" id="map_{{ name }}" class="{{ class }}" data-geojson="{{ content | escape }}" data-mousewheel="0" title="{{ placeholder }}" style="height:360px"></div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Attribution Component.
     * Renders a list of key-value pairs (attributes).
     *
     * @return  array Returns component configuration array
     */
    public function attribution(): array
    {
        // Template for Attribute List
        $component = <<<EOF
        <div class="w-100">
            {% for label, value in content %}
                <div class="row mb-1 border-top">
                    <div class="col-12 col-sm-4">
                        <label class="text-muted">
                            {{ label }}
                        </label>
                    </div>
                    <div class="col-12 col-sm-8">
                        {{ value }}
                    </div>
                </div>
            {% endfor %}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Accordion Component.
     * Renders an accordion interface for displaying grouped content.
     *
     * @return  array Returns component configuration array
     */
    public function accordion(): array
    {
        // Template for Accordion
        $component = <<<EOF
        <div class="accordion" id="accordion_{{ name }}">
            {% for key, accordion in content %}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_{{ key }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ key }}" aria-controls="collapse_{{ key }}">
                            {{ accordion.title }}
                        </button>
                    </h2>
                    <div id="collapse_{{ key }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ key }}" data-bs-parent="#accordion_{{ name }}">
                        <div class="accordion-body">
                            {{ accordion.body }}
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Carousel Component.
     * Renders an image carousel/slider.
     *
     * @return  array Returns component configuration array
     */
    public function carousel(): array
    {
        // Template for Image Carousel
        $component = <<<EOF
        <div>
            <div id="carousel_{{ name }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    {% for key, carousel in content %}
                        <div class="carousel-item {% if key is same as(0) %} active {% endif %}">
                            <img src="{{ carousel.src.background }}" class="d-block w-100 rounded" alt="...">
                            <div class="carousel-caption">
                                <h5 class="text-secondary">
                                    {{ carousel.title }}
                                </h5>
                                <p class="text-secondary">
                                    {{ carousel.description }}
                                </p>
                            </div>
                        </div>
                    {% endfor %}
                </div>
                <a class="carousel-control-prev" href="#carousel_{{ name }}" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </a>
                <a class="carousel-control-next" href="#carousel_{{ name }}" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </a>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Custom Format Component.
     * Passthrough for raw content rendering.
     *
     * @return  array Returns component configuration array
     */
    public function custom_format(): array
    {
        // Template for Raw Content
        $component = <<<EOF
        {{ content | raw }}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }
}
