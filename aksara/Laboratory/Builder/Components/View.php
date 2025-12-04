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

class View
{
    public function __construct()
    {
        // Safe abstraction
    }

    public function text($type = null)
    {
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

    public function textarea($type = null)
    {
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

    public function wysiwyg($type = null)
    {
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

    public function number($type = null)
    {
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

    public function money($type = null)
    {
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

    public function percent($type = null)
    {
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

    public function select($type = null)
    {
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

    public function checkbox($type = null)
    {
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

    public function radio($type = null)
    {
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

    public function boolean($type = null)
    {
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

    public function range($type = null)
    {
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

    public function color($type = null)
    {
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

    public function date($type = null)
    {
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

    public function datetime($type = null)
    {
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

    public function time($type = null)
    {
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

    public function week($type = null)
    {
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

    public function month($type = null)
    {
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

    public function hidden($type = null)
    {
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

    public function email($type = null)
    {
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

    public function password($type = null)
    {
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

    public function encryption($type = null)
    {
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

    public function file($type = null)
    {
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

    public function files($type = null)
    {
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

    public function image($type = null)
    {
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

    public function images($type = null)
    {
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

    public function hyperlink($type = null)
    {
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

    public function geospatial($type = null)
    {
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

    public function attribution($type = null)
    {
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

    public function accordion($type = null)
    {
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

    public function carousel($type = null)
    {
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

    public function custom_format($type = null)
    {
        $component = <<<EOF
        {{ content | raw }}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }
}
