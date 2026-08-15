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

namespace Aksara\Laboratory\Builder\Components;

/**
 * Table Column Component Builder
 *
 * This class contains raw Twig templates used to render data inside
 * table cells (<td>) for the Table View (Index).
 * It handles formatting for various data types like money, images, dates,
 * and badges for booleans.
 */
class Table
{
    /**
     * Generate Text Column.
     * Renders plain text with optional HTML escaping.
     */
    public function text(): array
    {
        // Template for Plain Text
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

    /**
     * Generate Textarea/Long Text Column.
     * Renders text truncated to 64 characters to prevent table bloating.
     */
    public function textarea(): array
    {
        // Template for Truncated Text
        $component = <<<EOF
        <span>{{ truncate ? truncate(value, 64) : value }}</span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate WYSIWYG Content Column.
     * Renders rich text content (HTML allowed).
     */
    public function wysiwyg(): array
    {
        // Template for Rich Text
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

    /**
     * Generate Number Column.
     * Renders integer/numeric values.
     */
    public function number(): array
    {
        // Template for Number
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

    /**
     * Generate Money Column.
     * Renders currency values, right-aligned (float-end).
     */
    public function money(): array
    {
        // Template for Currency (Right Aligned)
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

    /**
     * Generate Percent Column.
     * Renders a percentage formatted number.
     */
    public function percent(): array
    {
        // Template for Percentage
        $component = <<<EOF
        <span class="float-end">
            {{ escape ? content : content | raw }}%
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Select/Dropdown Column.
     * Renders the label associated with the selected option value.
     */
    public function select(): array
    {
        // Template for Select Label
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

    /**
     * Generate Checkbox Column.
     * Renders the values of selected checkboxes.
     */
    public function checkbox(): array
    {
        // Template for Checkbox Values
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

    /**
     * Generate Radio Column.
     * Renders the value of the selected radio button.
     */
    public function radio(): array
    {
        // Template for Radio Value
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

    /**
     * Generate Boolean Column.
     * Renders a badge (Green/Red) indicating Active/Inactive status.
     */
    public function boolean(): array
    {
        // Template for Status Badge
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

    /**
     * Generate Range Column.
     * Renders a disabled slider input to visualize the value.
     */
    public function range(): array
    {
        // Template for Range Visualization
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

    /**
     * Generate Color Column.
     * Renders a disabled color input to visualize the selected color.
     */
    public function color(): array
    {
        // Template for Color Sample
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

    /**
     * Generate Date Column.
     * Renders a date string.
     */
    public function date(): array
    {
        // Template for Date
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

    /**
     * Generate DateTime Column.
     * Renders a date and time string.
     */
    public function datetime(): array
    {
        // Template for DateTime
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

    /**
     * Generate Time Column.
     * Renders a time string.
     */
    public function time(): array
    {
        // Template for Time
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

    /**
     * Generate Week Column.
     * Renders a week number/string.
     */
    public function week(): array
    {
        // Template for Week
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

    /**
     * Generate Month Column.
     * Renders a month string.
     */
    public function month(): array
    {
        // Template for Month
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

    /**
     * Generate Hidden Column.
     * Renders a placeholder text "Hidden" for obscured fields.
     */
    public function hidden(): array
    {
        // Template for Hidden Field Placeholder
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

    /**
     * Generate Email Column.
     * Renders an email address.
     */
    public function email(): array
    {
        // Template for Email
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

    /**
     * Generate Password Column.
     * Renders asterisks to mask the password value.
     */
    public function password(): array
    {
        // Template for Masked Password
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

    /**
     * Generate Encryption Column.
     * Renders an encrypted string (typically masked or raw hash).
     */
    public function encryption(): array
    {
        // Template for Encrypted String
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

    /**
     * Generate File Link Column.
     * Renders a clickable link to download/view a file, with truncated text.
     */
    public function file(): array
    {
        // Template for File Link
        $component = <<<EOF
        <span>
            <a href="{{ content }}" class="{{ class }}" target="{{ target }}">
                <b>{{ truncate ? truncate(value, 32) : value }}</b>
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate File Count Column.
     * Renders the count of files for multi-file fields.
     */
    public function files(): array
    {
        // Template for Multiple Files Count
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

    /**
     * Generate Image Thumbnail Column.
     * Renders a small thumbnail image linked to the full version.
     */
    public function image(): array
    {
        // Template for Image Thumbnail
        $component = <<<EOF
        <span class="d-flex align-items-center justify-content-center">
            <a href="{{ content | replace({'/thumbs/': '/'}) }}" class="{{ class }}" target="_blank">
                <img src="{{ content }}" class="img-fluid rounded" width="22" height="22" alt="{{ label ? label : phrase('Thumbnail') }}" loading="lazy" decoding="async">
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Image Count Column.
     * Renders the count of images for multi-image fields.
     */
    public function images(): array
    {
        // Template for Multiple Images Count
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

    /**
     * Generate Hyperlink Column.
     * Renders a custom URL link with an external icon.
     */
    public function hyperlink(): array
    {
        // Template for Custom Link
        $component = <<<EOF
        <span>
            <a href="{{ content }}" class="--xhr" target="{{ target }}">
                <b>{{ truncate ? truncate(value, 32) : value }}<i class="mdi mdi-launch"></i></b>
            </a>
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Geospatial Placeholder Column.
     * Renders a placeholder text indicating geospatial data exists.
     */
    public function geospatial(): array
    {
        // Template for GeoJSON Placeholder
        $component = <<<EOF
        <span>
            [VECTOR TILE]
        </span>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Attribution Count Column.
     * Renders a badge showing the number of attributes.
     */
    public function attribution(): array
    {
        // Template for Attribution Count Badge
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

    /**
     * Generate Custom Format Column.
     * Passthrough component for raw content rendering.
     */
    public function custom(): array
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
