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
 * Form Component Builder
 *
 * This class contains raw Twig templates used to generate various form input fields.
 * It covers standard HTML5 inputs (text, number, date) as well as complex
 * components like WYSIWYG editors, file uploaders, maps, and dynamic repeaters.
 */
class Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // No initialization required
    }

    /**
     * Generate Text Input Component.
     * Renders a standard single-line text input field.
     *
     * @return  array Returns component configuration array
     */
    public function text(): array
    {
        // Template for Text Input
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="text" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Textarea Component.
     * Renders a multi-line text input field.
     *
     * @return  array Returns component configuration array
     */
    public function textarea(): array
    {
        // Template for Textarea
        $component = <<<EOF
        <textarea name="{{ name }}" role="textarea" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" rows="1" {{ attribution | raw }} {{ readonly }}>{{ value }}</textarea>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate WYSIWYG Editor Component.
     * Renders a textarea initialized with a rich text editor role.
     *
     * @return  array Returns component configuration array
     */
    public function wysiwyg(): array
    {
        // Template for WYSIWYG (Rich Text) Editor
        $component = <<<EOF
        <div class="w-100">
            <textarea name="{{ name }}" role="wysiwyg" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" rows="1" {{ readonly }}>{{ value | raw }}</textarea>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Number Input Component.
     * Renders an input field that accepts integers.
     *
     * @return  array Returns component configuration array
     */
    public function number(): array
    {
        // Template for Integer Input
        $component = <<<EOF
        <input type="number" name="{{ name }}" role="number" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="1" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Money/Currency Input Component.
     * Renders an input field optimized for currency values (allows decimals).
     *
     * @return  array Returns component configuration array
     */
    public function money(): array
    {
        // Template for Currency Input
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="money" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="0.01" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Percent Input Component.
     * Renders an input field for percentage values.
     *
     * @return  array Returns component configuration array
     */
    public function percent(): array
    {
        // Template for Percentage Input
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="money" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="0.01" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Select Dropdown Component.
     * Renders a dropdown list populated with options from the content array.
     *
     * @return  array Returns component configuration array
     */
    public function select(): array
    {
        // Template for Select/Dropdown
        $component = <<<EOF
        <select name="{{ name }}" role="select" data-relation="{{ relation }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" {{ attribution | raw }} {{ readonly }}>
            {% for option in content %}
                <option value="{{ option.value }}" {% if option.selected %} selected {% endif %}>{{ option.label }}</option>
            {% endfor %}
        </select>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Checkbox Group Component.
     * Renders a list of checkboxes for multiple selections.
     *
     * @return  array Returns component configuration array
     */
    public function checkbox(): array
    {
        // Template for Checkbox Group
        $component = <<<EOF
        {% for option in content %}
            <div class="form-check me-3">
                <label class="form-check-label {{ option.class }}">
                    <input type="checkbox" name="{{ name }}[]" role="checkbox" value="{{ option.value }}" class="form-check-input {{ option.class }}" id="{{ option.value }}_input" {{ attribution | raw }} {{ option.readonly }} {% if option.checked %} checked {% endif %}>
                    {{ option.label | raw }}
                </label>
            </div>
        {% endfor %}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Radio Button Group Component.
     * Renders a list of radio buttons for single selection.
     *
     * @return  array Returns component configuration array
     */
    public function radio(): array
    {
        // Template for Radio Button Group
        $component = <<<EOF
        {% for option in content %}
            <div class="form-check me-3">
                <label class="form-check-label {{ option.class }}">
                    <input type="radio" name="{{ name }}" role="radio" value="{{ option.value }}" class="form-check-input {{ option.class }}" {{ attribution | raw }} {{ option.readonly }} {% if option.checked %} checked {% endif %}>
                    {{ option.label | raw }}
                </label>
            </div>
        {% endfor %}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Boolean Switch Component.
     * Renders a toggle switch (styled checkbox) for binary values (1/0).
     *
     * @return  array Returns component configuration array
     */
    public function boolean(): array
    {
        // Template for Boolean Switch
        $component = <<<EOF
        <div class="form-check form-switch">
            <input type="checkbox" name="{{ name }}" role="boolean" value="1" class="form-check-input {{ class }}" id="{{ name }}_input" {{ attribution | raw }} {{ readonly }} {% if checked %} checked {% endif %}>
            <label class="form-check-label" for="{{ name }}_input"> {{ placeholder }} </label>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Range Slider Component.
     * Renders a slider input for selecting a value within a range (0-100).
     *
     * @return  array Returns component configuration array
     */
    public function range(): array
    {
        // Template for Range Slider
        $component = <<<EOF
        <input type="range" name="{{ name }}" role="range" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" min="0" max="100" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Color Picker Component.
     * Renders a color selection input.
     *
     * @return  array Returns component configuration array
     */
    public function color(): array
    {
        // Template for Color Picker
        $component = <<<EOF
        <input type="color" name="{{ name }}" role="color" value="{{ value }}" class="form-control form-control-color {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Date Input Component.
     * Renders an input for selecting a date (YYYY-MM-DD).
     *
     * @return  array Returns component configuration array
     */
    public function date(): array
    {
        // Template for Date Input
        $component = <<<EOF
        <input type="date" name="{{ name }}" role="date" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate DateTime Input Component.
     * Renders an input for selecting both date and time.
     *
     * @return  array Returns component configuration array
     */
    public function datetime(): array
    {
        // Template for DateTime Input
        $component = <<<EOF
        <input type="datetime-local" name="{{ name }}" role="datetime" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Time Input Component.
     * Renders an input for selecting time.
     *
     * @return  array Returns component configuration array
     */
    public function time(): array
    {
        // Template for Time Input
        $component = <<<EOF
        <input type="time" step="1" name="{{ name }}" role="time" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Week Input Component.
     * Renders an input for selecting a specific week number in a year.
     *
     * @return  array Returns component configuration array
     */
    public function week(): array
    {
        // Template for Week Input
        $component = <<<EOF
        <input type="week" name="{{ name }}" role="week" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Month Input Component.
     * Renders an input for selecting a specific month in a year.
     *
     * @return  array Returns component configuration array
     */
    public function month(): array
    {
        // Template for Month Input
        $component = <<<EOF
        <input type="month" name="{{ name }}" role="month" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Hidden Input Component.
     * Renders a hidden input field for passing data silently.
     *
     * @return  array Returns component configuration array
     */
    public function hidden(): array
    {
        // Template for Hidden Input
        $component = <<<EOF
        <input type="hidden" name="{{ name }}" role="hidden" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Email Input Component.
     * Renders an input field validated for email addresses.
     *
     * @return  array Returns component configuration array
     */
    public function email(): array
    {
        // Template for Email Input
        $component = <<<EOF
        <input type="email" name="{{ name }}" role="email" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Password Input Component.
     * Renders a dual password field (input + confirmation) for setting passwords.
     *
     * @return  array Returns component configuration array
     */
    public function password(): array
    {
        // Template for Password with Confirmation
        $component = <<<EOF
        <input type="password" name="{{ name }}" role="password" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ phrase('Leave blank to ignore') }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        <input type="password" name="{{ name }}_confirmation" role="password" class="form-control {{ class }}" id="{{ name }}_confirmation_input" placeholder="{{ phrase('Retype') }} {{ name }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Encryption Input Component.
     * Renders a single password field, typically used for encrypted values/tokens.
     *
     * @return  array Returns component configuration array
     */
    public function encryption(): array
    {
        // Template for Encrypted Field (Single Password Input)
        $component = <<<EOF
        <input type="password" name="{{ name }}" role="password" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ phrase('Leave blank to ignore') }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate File Input Component.
     * Renders a standard single file upload input.
     *
     * @return  array Returns component configuration array
     */
    public function file(): array
    {
        // Template for Standard File Input
        $component = <<<EOF
        <input type="file" name="{{ name }}" role="file" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ attribution | raw }} {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Multi-File Uploader Component.
     * Renders a custom file uploader supporting multiple files.
     *
     * @return  array Returns component configuration array
     */
    public function files(): array
    {
        // Template for Multiple File Uploader
        $component = <<<EOF
        <div class="uploader-input w-100">
            <input type="file" name="{{ name }}[]" class="custom-file-input d-none" role="uploader" id="{{ name }}_input" data-fileuploader-files="{{ content | json_encode | escape }}" accept="{{ accept }}" {{ attribution | raw }} multiple />
            <label class="form-control custom-file-label" for="{{ name }}_input">{{ label }}</label>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Image Uploader Component.
     * Renders a single image uploader with preview functionality.
     *
     * @return  array Returns component configuration array
     */
    public function image(): array
    {
        // Template for Single Image Upload with Preview
        $component = <<<EOF
        <div data-provides="fileupload" class="fileupload fileupload-new text-sm-center {% if content and '/thumbs/' not in content %}w-100{% endif %}">
            <span class="btn btn-file d-block">
                <input type="file" name="{{ name }}" accept="{{ accept }}" role="image-upload" id="{{ name }}_input" {{ attribution | raw }} {{ readonly }} />
                <div class="fileupload-new text-center {% if content and '/thumbs/' not in content %}w-100{% endif %}">
                    <img class="img-fluid upload_preview" src="{{ content }}" />
                </div>
                <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.fileupload').find('input[type=file]').val(''), $(this).closest('.fileupload').find('img').attr('src', '{{ placeholder }}'))">
                    <i class="mdi mdi-delete"></i>
                </button>
            </span>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Multi-Image Uploader Component.
     * Renders a custom uploader specifically optimized for multiple images.
     *
     * @return  array Returns component configuration array
     */
    public function images(): array
    {
        // Template for Multiple Image Uploader
        $component = <<<EOF
        <div class="uploader-input w-100">
            <input type="file" name="{{ name }}[]" class="custom-file-input d-none" role="uploader" id="{{ name }}_input" data-fileuploader-files="{{ content | json_encode | escape }}" accept="{{ accept }}" {{ attribution | raw }} multiple />
            <label class="form-control custom-file-label" for="{{ name }}_input">{{ label }}</label>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Geospatial/Map Component.
     * Renders a map interface for selecting coordinates or drawing geo-shapes.
     *
     * @return  array Returns component configuration array
     */
    public function geospatial(): array
    {
        // Template for Map/Geospatial Input
        $component = <<<EOF
        <div class="drawing-placeholder preloader position-relative w-100 overflow-hidden">
            <div role="map" id="map_{{ name }}" class="{{ class }}" data-coordinate="{{ value | escape }}" data-geojson="{{ content | escape }}" data-apply-coordinate-to="#{{ name }}_input" data-apply-address-to=".map-address-listener" data-geocoder="1" data-mousewheel="0" title="{{ placeholder }}" {{ attribution | raw }} style="height:360px"></div>
            <input type="hidden" name="{{ name }}" id="{{ name }}_input" value="{{ content | escape }}" {{ readonly }} />
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Attribution Input Component.
     * Renders a dynamic repeater field for Key-Value pair attributes.
     *
     * @return  array Returns component configuration array
     */
    public function attribution(): array
    {
        // Template for Dynamic Attribute Repeater (Key-Value)
        $component = <<<EOF
        <div class="attribution-input w-100">
            <div class="attribution-input-body">
                {% for label, value in content %}
                    <div class="row mb-1">
                        <div class="col-4 pe-0">
                            <input type="text" name="{{ name }}[label][]" value="{{ label }}" class="form-control form-control-sm" placeholder="{{ phrase('Label') }}" autocomplete="off" spellcheck="false" />
                        </div>
                        <div class="col-5 pe-0">
                            <input type="text" name="{{ name }}[value][]" value="{{ value }}" class="form-control form-control-sm" placeholder="{{ phrase('Value') }}" autocomplete="off" spellcheck="false" />
                        </div>
                        <div class="col-3">
                            <div class="btn-group btn-group-sm float-end">
                                <a href="javascript:void(0)" class="btn btn-secondary --move-up" data-element=".row" data-bs-toggle="tooltip" title="{{ phrase('Move Up') }}">
                                    <i class="mdi mdi-arrow-collapse-up"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-secondary --move-down" data-element=".row" data-bs-toggle="tooltip" title="{{ phrase('Move Down') }}">
                                    <i class="mdi mdi-arrow-collapse-down"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-secondary" role="remove-attribution" data-element=".row">
                                    <i class="mdi mdi-delete" data-bs-toggle="tooltip" title="{{ phrase('Remove') }}"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
            <div class="row">
                <div class="col-4 pe-0">
                    <div class="d-grid">
                        <button type="button" class="btn btn-secondary btn-sm d-block" role="add-attribution" data-label="{{ name }}[label][]" data-label-placeholder="{{ phrase('Label') }}" data-value-placeholder="{{ phrase('Value') }}" data-value="{{ name }}[value][]">
                            <i class="mdi mdi-plus-circle-outline"></i>
                            &nbsp;
                            {{ phrase('Add') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Accordion Component.
     * Renders a dynamic repeater field for Title-Body content structures.
     *
     * @return  array Returns component configuration array
     */
    public function accordion(): array
    {
        // Template for Dynamic Accordion Repeater
        $component = <<<EOF
        <div class="w-100">
            {% for key, accordion in content %}
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div class="input-group input-group-sm">
                            <input type="text" name="{{ name }}[title][]" class="form-control" placeholder="{{ phrase('Title') }}" value="{{ accordion.title }}" id="{{ name }}_input" spellcheck="false" />
                            <a href="javascript:void(0)" class="btn btn-secondary --move-up" data-element=".card" data-bs-toggle="tooltip" title="{{ phrase('Move Up') }}">
                                <i class="mdi mdi-arrow-collapse-up"></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-secondary --move-down" data-element=".card" data-bs-toggle="tooltip" title="{{ phrase('Move Down') }}">
                                <i class="mdi mdi-arrow-collapse-down"></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-secondary" role="remove-accordion" data-element=".card">
                                <i class="mdi mdi-delete" data-bs-toggle="tooltip" title="{{ phrase('Remove') }}"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <textarea name="{{ name }}[body][]" class="form-control" role="wysiwyg" placeholder="{{ phrase('Body of item') }}" id="{{ name }}_input" rows="1" spellcheck="false" {{ readonly }}>{{ accordion.body }}</textarea>
                    </div>
                </div>
            {% endfor %}
        </div>
        <div class="d-grid w-100">
            <a href="javascript:void(0)" class="btn btn-light" role="add-accordion" data-field="{{ name }}" style="border:2px dashed #ddd">
                <i class="mdi mdi-plus-circle-outline"></i>
                {{ phrase('Add') }}
            </a>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Carousel Component.
     * Renders a dynamic repeater for creating image sliders/carousels with metadata.
     *
     * @return  array Returns component configuration array
     */
    public function carousel(): array
    {
        // Template for Dynamic Carousel/Slider Repeater
        $component = <<<EOF
        <div class="w-100">
            {% for key, carousel in content %}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted">
                                {{ phrase('Background') }}
                            </label>
                            <div data-provides="fileupload" class="fileupload fileupload-new text-sm-center mb-3">
                                <span class="btn btn-file d-block">
                                    <input type="file" name="{{ name }}[background][{{ key }}]" accept="{{ accept }}" role="image-upload" id="{{ name }}_input" {{ readonly }} />
                                    <div class="fileupload-new text-center">
                                        <img class="img-fluid upload_preview rounded" src="{{ carousel.src.background }}" alt="..." />
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.fileupload').find('input[type=file]').val(''), $(this).closest('.fileupload').find('img').attr('src', '{{ carousel.src.placeholder }}'))"><i class="mdi mdi-delete"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="{{ name }}[title][{{ key }}]" class="form-control" placeholder="{{ phrase('Title') }}" value="{{ carousel.title }}" id="{{ name }}_input" spellcheck="false" {{ readonly }} />
                        </div>
                        <div class="mb-3">
                            <textarea name="{{ name }}[description][{{ key }}]" class="form-control" placeholder="{{ phrase('Description') }}" id="{{ name }}_input" rows="1" spellcheck="false" {{ readonly }}>{{ carousel.description }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" name="{{ name }}[link][{{ key }}]" class="form-control" placeholder="{{ phrase('Target URL') }}" value="{{ carousel.link }}" id="{{ name }}_input" spellcheck="false" {{ readonly }} />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" name="{{ name }}[label][{{ key }}]" class="form-control" placeholder="{{ phrase('Button Label') }}" value="{{ carousel.label }}" id="{{ name }}_input" spellcheck="false" {{ readonly }} />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pt-1 pb-1">
                        <input type="hidden" name="{{ name }}[default_background][{{ key }}]" value="{{ carousel.background }}" {{ readonly }} />
                        <div class="btn-group btn-group-sm">
                            <a href="javascript:void(0)" class="btn btn-secondary {% if readonly is empty %} --move-up {% endif %}" data-element=".card" data-bs-toggle="tooltip" title="{{ phrase('Move Up') }}">
                                <i class="mdi mdi-arrow-collapse-up"></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-secondary {% if readonly is empty %} --move-down {% endif %}" data-element=".card" data-bs-toggle="tooltip" title="{{ phrase('Move Down') }}">
                                <i class="mdi mdi-arrow-collapse-down"></i>
                            </a>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm float-end" {% if readonly is empty %} role="remove-carousel" {% endif %} data-element=".card">
                            <i class="mdi mdi-delete" data-bs-toggle="tooltip" title="{{ phrase('Remove') }}"></i>
                        </a>
                    </div>
                </div>
            {% endfor %}
            <div class="d-grid">
                <a href="javascript:void(0)" class="btn btn-light d-block" {% if readonly is empty %} role="add-carousel" {% endif %} data-field="{{ name }}" data-image-placeholder="{{ placeholder }}" style="border:2px dashed #ddd">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    {{ phrase('Add') }}
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
     * Passthrough component to render raw content without standard wrappers.
     *
     * @return  array Returns component configuration array
     */
    public function custom_format(): array
    {
        // Passthrough for Raw Content
        $component = <<<EOF
        {{ content | raw }}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }
}
