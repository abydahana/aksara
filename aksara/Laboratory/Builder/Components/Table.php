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

class Table
{
    public function __construct()
    {
        // Safe abstraction
    }

    public function text($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function textarea($type = null)
    {
        $component = <<<EOF
        <span>
            {{ truncate(value, 64) }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function wysiwyg($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function number($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function money($type = null)
    {
        $component = <<<EOF
        <span class="float-end">
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function percent($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | format_percent_number }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function select($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function checkbox($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function radio($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function boolean($type = null)
    {
        $component = <<<EOF
        <span class="badge {% if value %} bg-success {% else %} bg-danger {% endif %}">
            {% if value %}
                {{ phrase('Active') }}
            {% else %}
                {{ phrase('Inactive') }}
            {% endif %}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function range($type = null)
    {
        $component = <<<EOF
        <span>
            <input type="range" value="{{ value }}" min="0" max="100" disabled>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function color($type = null)
    {
        $component = <<<EOF
        <span>
            <input type="color" value="{{ value }}" disabled>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function date($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function datetime($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function time($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function week($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function month($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function hidden($type = null)
    {
        $component = <<<EOF
        <span>
            {{ phrase('Hidden') }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function email($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function password($type = null)
    {
        $component = <<<EOF
        <span>
            ******
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function encryption($type = null)
    {
        $component = <<<EOF
        <span>
            {{ escape ? content : content | raw }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function file($type = null)
    {
        $component = <<<EOF
        <span>
            <a href="{{ content }}" class="{{ class }}" target="{{ target }}">
                <b>{{ truncate(value, 32) }}</b>
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function files($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | length }} {{ content | length > 1 ? phrase('files') : phrase('file') }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function image($type = null)
    {
        $component = <<<EOF
        <span>
            <a href="{{ content | replace({'/thumbs/': '/'}) }}" class="{{ class }}" target="_blank">
                <img src="{{ content }}" class="img-fluid rounded" width="22" height="22" alt="...">
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function images($type = null)
    {
        $component = <<<EOF
        <span>
            {{ content | length }} {{ content | length > 1 ? phrase('images') : phrase('image') }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function hyperlink($type = null)
    {
        $component = <<<EOF
        <span>
            <a href="{{ content }}" class="--xhr" target="{{ target }}">
                <b> {{ truncate(value, 32) }}<i class="mdi mdi-launch"></i> </b>
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function geospatial($type = null)
    {
        $type = 'geospatial';

        $component = <<<EOF
        <span>
            [GEOJSON]
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function attribution($type = null)
    {
        $component = <<<EOF
        <span class="badge bg-secondary">
            {{ content | length }} {{ content | length > 1 ? phrase('attributes') : phrase('attribute') }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function accordion($type = null)
    {
        $component = <<<EOF
        <span class="badge bg-secondary">
            {{ content | length }} {{ content | length > 1 ? phrase('items') : phrase('item') }}
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function carousel($type = null)
    {
        $component = <<<EOF
        <span class="badge bg-secondary">
            {{ content | length }} {{ content | length > 1 ? phrase('slides') : phrase('slide') }}
        </span>
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
