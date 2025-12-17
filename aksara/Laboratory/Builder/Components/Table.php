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
     * Constructor
     */
    public function __construct()
    {
        // No initialization required
    }

    /**
     * Generate Text Column.
     * Renders plain text with optional HTML escaping.
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
     */
    public function textarea(): array
    {
        // Template for Truncated Text
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

    /**
     * Generate WYSIWYG Content Column.
     * Renders rich text content (HTML allowed).
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
     */
    public function percent(): array
    {
        // Template for Percentage
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

    /**
     * Generate Select/Dropdown Column.
     * Renders the label associated with the selected option value.
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
     */
    public function file(): array
    {
        // Template for File Link
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

    /**
     * Generate File Count Column.
     * Renders the count of files for multi-file fields.
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
     */
    public function image(): array
    {
        // Template for Image Thumbnail
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

    /**
     * Generate Image Count Column.
     * Renders the count of images for multi-image fields.
     *
     * @return  array Returns component configuration array
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
     *
     * @return  array Returns component configuration array
     */
    public function hyperlink(): array
    {
        // Template for Custom Link
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

    /**
     * Generate Geospatial Placeholder Column.
     * Renders a placeholder text indicating geospatial data exists.
     *
     * @return  array Returns component configuration array
     */
    public function geospatial(): array
    {
        // Template for GeoJSON Placeholder
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

    /**
     * Generate Attribution Count Column.
     * Renders a badge showing the number of attributes.
     *
     * @return  array Returns component configuration array
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
     * Generate Accordion Count Column.
     * Renders a badge showing the number of accordion items.
     *
     * @return  array Returns component configuration array
     */
    public function accordion(): array
    {
        // Template for Accordion Items Count Badge
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

    /**
     * Generate Carousel Count Column.
     * Renders a badge showing the number of slides.
     *
     * @return  array Returns component configuration array
     */
    public function carousel(): array
    {
        // Template for Carousel Slides Count Badge
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

    /**
     * Generate Custom Format Column.
     * Passthrough component for raw content rendering.
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
