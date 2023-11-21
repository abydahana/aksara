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

class Form
{
    public function __construct()
    {
        // Safe abstraction
    }

    public function text($type = null)
    {
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="text" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function textarea($type = null)
    {
        $component = <<<EOF
        <textarea name="{{ name }}" role="textarea" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" rows="1" {{ readonly }}>{{ value }}</textarea>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function wysiwyg($type = null)
    {
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

    public function number($type = null)
    {
        $component = <<<EOF
        <input type="number" name="{{ name }}" role="number" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="1" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function decimal($type = null)
    {
        $component = <<<EOF
        <input type="number" name="{{ name }}" role="decimal" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="0.01" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function currency($type = null)
    {
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="currency" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="0.01" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function percent($type = null)
    {
        $component = <<<EOF
        <input type="text" name="{{ name }}" role="currency" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" step="0.01" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function select($type = null)
    {
        $component = <<<EOF
        <select name="{{ name }}" role="select" data-relation="{{ relation }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" {{ readonly }}>
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

    public function checkbox($type = null)
    {
        $component = <<<EOF
        {% for option in content %}
            <div class="form-check me-3">
                <label class="form-check-label {{ option.class }}">
                    <input type="checkbox" name="{{ name }}[]" role="checkbox" value="{{ option.value }}" class="form-check-input {{ option.class }}" id="{{ option.value }}_input" {{ option.readonly }} {% if option.checked %} checked {% endif %}>
                    {{ option.label }}
                </label>
            </div>
        {% endfor %}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function radio($type = null)
    {
        $component = <<<EOF
        {% for option in content %}
            <div class="form-check me-3">
                <label class="form-check-label {{ option.class }}"> 
                    <input type="radio" name="{{ name }}" role="radio" value="{{ option.value }}" class="form-check-input {{ option.class }}" {{ option.readonly }} {% if option.checked %} checked {% endif %}>
                    {{ option.label }}
                </label>
            </div>
        {% endfor %}
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function boolean($type = null)
    {
        $component = <<<EOF
        <div class="form-check form-switch">
            <input type="checkbox" name="{{ name }}" role="boolean" value="1" class="form-check-input {{ class }}" id="{{ name }}_input" {{ readonly }} {% if checked %} checked {% endif %}>
            <label class="form-check-label" for="{{ name }}_input"> {{ placeholder }} </label>
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
        <input type="range" name="{{ name }}" role="range" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" min="0" max="100" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function color($type = null)
    {
        $component = <<<EOF
        <input type="color" name="{{ name }}" role="color" value="{{ value }}" class="form-control form-control-color {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function date($type = null)
    {
        $component = <<<EOF
        <input type="date" name="{{ name }}" role="date" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function datetime($type = null)
    {
        $component = <<<EOF
        <input type="datetime-local" name="{{ name }}" role="datetime" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function time($type = null)
    {
        $component = <<<EOF
        <input type="time" name="{{ name }}" role="time" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function week($type = null)
    {
        $component = <<<EOF
        <input type="week" name="{{ name }}" role="week" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function month($type = null)
    {
        $component = <<<EOF
        <input type="month" name="{{ name }}" role="month" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function hidden($type = null)
    {
        $component = <<<EOF
        <input type="hidden" name="{{ name }}" role="hidden" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function email($type = null)
    {
        $component = <<<EOF
        <input type="email" name="{{ name }}" role="email" value="{{ value }}" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function password($type = null)
    {
        $component = <<<EOF
        <input type="password" name="{{ name }}" role="password" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ phrase('Leave blank to ignore') }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        <input type="password" name="{{ name }}_confirmation" role="password" class="form-control {{ class }}" id="{{ name }}_confirmation_input" placeholder="{{ phrase('Retype') }} {{ name }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function encryption($type = null)
    {
        $component = <<<EOF
        <input type="password" name="{{ name }}" role="password" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ phrase('Leave blank to ignore') }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function file($type = null)
    {
        $component = <<<EOF
        <input type="file" name="{{ name }}" role="file" class="form-control {{ class }}" id="{{ name }}_input" placeholder="{{ placeholder }}" maxlength="{{ maxlength }}" spellcheck="false" {{ readonly }}>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function files($type = null)
    {
        $component = <<<EOF
        <div class="uploader-input w-100">
            <input type="file" name="{{ name }}[]" class="custom-file-input d-none" role="uploader" id="{{ name }}_input" data-fileuploader-files="{{ content | json_encode | escape }}" accept="{{ accept }}" multiple />
            <label class="form-control custom-file-label" for="{{ name }}_input">{{ label }}</label>
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
        <div data-provides="fileupload" class="fileupload fileupload-new text-sm-center w-100">
            <span class="btn btn-file d-block">
                <input type="file" name="{{ name }}" accept="{{ accept }}" role="image-upload" id="{{ name }}_input"{{ readonly }} />
                <div class="fileupload-new text-center">
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

    public function images($type = null)
    {
        $component = <<<EOF
        <div class="uploader-input w-100">
            <input type="file" name="{{ name }}[]" class="custom-file-input d-none" role="uploader" id="{{ name }}_input" data-fileuploader-files="{{ content | json_encode | escape }}" accept="{{ accept }}" multiple />
            <label class="form-control custom-file-label" for="{{ name }}_input">{{ label }}</label>
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
            <div role="map" id="map_{{ name }}" class="{{ class }}" data-coordinate="{{ value | escape }}" data-geojson="{{ content | escape }}" data-apply-coordinate-to="#{{ name }}_input" data-apply-address-to=".map-address-listener" data-geocoder="1" data-mousewheel="0" title="{{ placeholder }}" {{ attribution | raw }} style="height:360px"></div>
            <input type="hidden" name="{{ name }}" id="{{ name }}_input" value="{{ content | escape }}" {{ readonly }} />
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
        <div class="attribution-input w-100">
            <div class="attribution-input-body">
                {% for attribution in content %}
                    <div class="row mb-1">
                        <div class="col-4 pe-0">
                            <input type="text" name="{{ name }}[label][]" value="{{ attribution.label }}" class="form-control form-control-sm" placeholder="{{ phrase('Label') }}" autocomplete="off" spellcheck="false" />
                        </div>
                        <div class="col-5 pe-0">
                            <input type="text" name="{{ name }}[value][]" value="{{ attribution.value }}" class="form-control form-control-sm" placeholder="{{ phrase('Value') }}" autocomplete="off" spellcheck="false" />
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

    public function accordion($type = null)
    {
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

    public function carousel($type = null)
    {
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
