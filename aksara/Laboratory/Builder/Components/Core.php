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

class Core
{
    public function __construct()
    {
        // Safe abstraction
    }

    public function index($type = null)
    {
        $component = <<<EOF
        <div class="container-fluid">
            <div role="toolbar" class="alias-table-toolbar py-1 border-bottom">
                {# Include toolbar component #}
                {% include 'core/toolbar.twig' with results.toolbar %}
            </div>
            <div role="table" class="table-responsive alias-table-index">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="1">
                                <div class="mb-0">
                                    <input type="checkbox" class="form-check-input bulk-delete" data-bs-toggle="tooltip" title="{{ phrase('Check All') }}" role="checker" data-parent="table">
                                </div>
                            </th>
                            <th>
                                {{ phrase('Options') }}
                            </th>
                            {% set colspan = 0 %}
                            {% for column in results.columns %}
                                {% set colspan = colspan + 1 %}
                                <th align="{{ column.align }}" class="no-wrap">
                                    {% if column.url %}
                                        <a href="{{ column.url }}" class="fw-bold {{ column.class }}">
                                            {{ column.label }}
                                            <i class="{{ column.icon }}"></i>
                                        </a>
                                    {% else %}
                                        <span class="fw-bold">
                                            {{ column.label }}
                                        </span>
                                    {% endif %}
                                </th>
                            {% endfor %}
                        </tr>
                    </thead>
                    <tbody>
                        {% set references = [] %}
                        {% for key, row in results.table_data %}
                            {% set unique_reference = '' %}
                            {% for reference in results.item_reference %}
                                {% if row.field_data[reference].value is not null %}
                                    {% set unique_reference = unique_reference ~ row.field_data[reference].value %}
                                    {% if unique_reference not in references %}
                                        <tr>
                                            <td colspan="2">&nbsp;</td>
                                            <td colspan="{{ colspan }}">
                                                <b class="text-primary">{{ row.field_data[reference].value }}</b>
                                            </td>
                                        </tr>
                                
                                        {% set references = references | merge([unique_reference]) %}
                                    {% endif %}
                                {% endif %}
                            {% endfor %}
                            <tr id="row_{{ key }}">
                                <td>
                                    {% if row.deleting %}
                                        <div class="mb-0">
                                            <input type="checkbox" name="bulk_delete[]" value="{{ row.primary | json_encode | escape }}" class="form-check-input">
                                        </div>
                                    {% endif %}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        {% for button in row.buttons %}
                                            <a href="{{ button.url }}" class="btn {{ button.class }}" data-bs-toggle="tooltip" title="{{ button.label }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                                <i class="{{ button.icon }}"></i>
                                            </a>
                                        {% endfor %}
                                        {% if row.dropdowns | length > 0 %}
                                            <a href="javascript:void(0)" class="btn btn-secondary --open-item-option" data-bs-toggle="tooltip" title="{{ phrase('More options') }}" data-options="{{ row.dropdowns | json_encode | escape }}">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                            </a>
                                        {% endif %}
                                    </div>
                                </td>
                                {% for field in row.field_data %}
                                    <td colspan="{{ field.colspan }}">
                                        {# Include table component #}
                                        {% include 'table/' ~ field.type ~ '.twig' with field %}
                                    </td>
                                {% endfor %}
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
            <div role="pagination" class="alias-pagination border-top py-2">
                {# Include pagination component #}
                {% include 'core/pagination.twig' with pagination %}
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function index_grid($type = null)
    {
        $component = <<<EOF
        <div role="toolbar" class="alias-table-toolbar py-2 border-bottom">
            <div class="container-fluid">
                {# Include toolbar component #}
                {% include 'core/toolbar.twig' with results.toolbar %}
            </div>
        </div>
        <div role="grid" class="pt-3">
            <div class="container-fluid">
                <div class="row">
                    {% for key, row in results.table_data %}
                        <div class="col-sm-6 col-md-4 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                                {% set break = false %}
                                {% for field in row.field_data %}
                                    {% if not break and field.type == 'images' %}
                                        <div id="slideshow_{{ key }}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                {% for carousel_key, carousel_item in field.content %}
                                                    <div class="carousel-item position-relative rounded-4 {% if carousel_key is same as(0) %} active {% endif %}">
                                                        <a href="{{ carousel_item.url }}" target="_blank">
                                                            <div class="clip gradient-top rounded-top"></div>
                                                            <img src="{{ carousel_item.thumbnail }}" class="d-block rounded w-100" alt="...">
                                                        </a>
                                                    </div>
                                                {% endfor %}
                                            </div>
                                            <a class="carousel-control-prev gradient-right" href="#slideshow_{{ key }}" role="button" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            </a>
                                            <a class="carousel-control-next gradient-left" href="#slideshow_{{ key }}" role="button" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            </a>
                                        </div>
                                        
                                        {% set break = true %}
                                    {% elseif not break and field.type == 'image' %}
                                        <a href="{{ field.content | replace({'/thumbs/': '/'}) }}" target="_blank">
                                            <img src="{{ field.content }}" class="d-block rounded w-100" alt="...">
                                        </a>
                                        
                                        {% set break = true %}
                                    {% endif %}
                                {% endfor %}
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        {% for field in row.field_data %}
                                            {% if field.type != 'image' and field.type != 'images' %}
                                                <li class="list-group-item px-0">
                                                    <label class="text-sm text-muted d-block">
                                                        {{ field.label }}
                                                    </label>
                                                    {# Include table component #}
                                                    {% include 'table/' ~ field.type ~ '.twig' with field %}
                                                </li>
                                            {% endif %}
                                        {% endfor %}
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group btn-group-sm d-flex">
                                        {% for button in row.buttons %}
                                            <a href="{{ button.url }}" class="btn {{ button.class | replace({'btn-': 'ignore-btn-'}) }} btn-light" data-bs-toggle="tooltip" title="{{ button.label }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                                <i class="{{ button.icon }}"></i>
                                            </a>
                                        {% endfor %}
                                        {% if row.dropdowns | length > 0 %}
                                            <a href="javascript:void(0)" class="btn btn-light --open-item-option" data-bs-toggle="tooltip" title="{{ phrase('More options') }}" data-options="{{ row.dropdowns | json_encode | escape }}">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                            </a>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        <div role="pagination" class="alias-pagination py-2 border-top">
            <div class="container-fluid">
                {# Include pagination component #}
                {% include 'core/pagination.twig' with pagination %}
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function index_mobile($type = null)
    {
        $component = <<<EOF
        <div role="grid" class="pt-3">
            <div class="container-fluid">
                <div class="row">
                    {% for key, row in results.table_data %}
                        <div class="col-sm-6 col-md-4 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                                {% set break = false %}
                                {% for field in row.field_data %}
                                    {% if not break and field.type == 'images' %}
                                        <div id="slideshow_{{ key }}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                {% for carousel_key, carousel_item in field.content %}
                                                    <div class="carousel-item position-relative rounded-4 {% if carousel_key is same as(0) %} active {% endif %}">
                                                        <a href="{{ carousel_item.url }}" target="_blank">
                                                            <div class="clip gradient-top rounded-top"></div>
                                                            <img src="{{ carousel_item.thumbnail }}" class="d-block rounded w-100" alt="...">
                                                        </a>
                                                    </div>
                                                {% endfor %}
                                            </div>
                                            <a class="carousel-control-prev gradient-right" href="#slideshow_{{ key }}" role="button" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            </a>
                                            <a class="carousel-control-next gradient-left" href="#slideshow_{{ key }}" role="button" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            </a>
                                        </div>
                                        
                                        {% set break = true %}
                                    {% elseif not break and field.type == 'image' %}
                                        <a href="{{ field.content | replace({'/thumbs/': '/'}) }}" target="_blank">
                                            <img src="{{ field.content }}" class="d-block rounded w-100" alt="...">
                                        </a>
                                        
                                        {% set break = true %}
                                    {% endif %}
                                {% endfor %}
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        {% for field in row.field_data %}
                                            {% if field.type != 'image' and field.type != 'images' %}
                                                <li class="list-group-item px-0">
                                                    <label class="text-sm text-muted d-block">
                                                        {{ field.label }}
                                                    </label>
                                                    {# Include table component #}
                                                    {% include 'table/' ~ field.type ~ '.twig' with field %}
                                                </li>
                                            {% endif %}
                                        {% endfor %}
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group btn-group-sm d-flex">
                                        {% for button in row.buttons %}
                                            <a href="{{ button.url }}" class="btn {{ button.class }}" data-bs-toggle="tooltip" title="{{ button.label }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                                <i class="{{ button.icon }}"></i>
                                            </a>
                                        {% endfor %}
                                        {% if row.dropdowns | length > 0 %}
                                            <a href="javascript:void(0)" class="btn btn-secondary --open-item-option" data-bs-toggle="tooltip" title="{{ phrase('More options') }}" data-options="{{ row.dropdowns | json_encode | escape }}">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                            </a>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        <div role="pagination" class="alias-pagination pb-3">
            <div class="container-fluid">
                {# Include pagination component #}
                {% include 'core/pagination.twig' with pagination %}
            </div>
        </div>
        <div role="toolbar" class="alias-table-toolbar py-1">
            <div class="container-fluid">
                {# Include toolbar component #}
                {% include 'core/toolbar_mobile.twig' with results.toolbar %}
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function toolbar($type = null)
    {
        $component = <<<EOF
        <div class="row">
            <div class="col">
                <div class="btn-group btn-group-sm">
                    {% for key, button in buttons %}
                        <a href="{{ button.url }}" class="btn {{ button.class }}" data-bs-toggle="tooltip" title="{{ button.label }}" {% if button.path == 'delete' %} data-bulk-delete="true" {% endif %} {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                            <i class="{{ button.icon }}"></i>
                            {% if button.path != 'delete' %} {{ button.label }} {% endif %}
                        </a>
                    {% endfor %}
                </div>
            </div>
            <div class="col">
                <form action="{{ action }}" method="GET" class="form-horizontal">
                    <div class="input-group input-group-sm">
                        {% for name, filter in filters %}
                            {% if filter.type == 'text' %}
                                <input type="text" name="{{ name }}" value="{{ filter.values }}" placeholder="{{ filter.label }}" class="form-control">
                            {% elseif filter.type == 'select' %}
                                <select name="{{ name }}" placeholder="{{ filter.label }}" class="form-control">
                                    {% for option in filter.values %}
                                    <option value="{{ option.id }}" {% if option.selected %} selected {% endif %}>{{ option.label }}</option>
                                    {% endfor %}
                                </select>
                            {% endif %}
                        {% endfor %}
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function toolbar_mobile($type = null)
    {
        $component = <<<EOF
        <div class="opt-btn-overlap-fix"></div>
        <div class="btn-group btn-group-sm rounded-0 opt-btn">
            {% for key, button in buttons %}
                {% if key <= 2 %}
                    <a href="{{ button.url }}" class="btn {{ button.class }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                        <i class="{{ button.icon }}"></i>
                        {{ button.label }}
                    </a>
                {% elseif key == 3 %}
                    <a href="javascript:void(0)" class="btn btn-secondary --open-item-option" data-options="{{ buttons | slice(3) | json_encode | escape }}">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        {{ phrase('More') }}
                    </a>
                {% endif %}
            {% endfor %}
        </div>
        <div class="modal --prevent-remove" id="searchModal" tabindex="-1" aria-labelledby="searchModalCenterTitle" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered ui-draggable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchModalCenterTitle">
                            <i class="mdi mdi-magnify"></i>
                            {{ phrase('Search data') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ action }}" method="GET">
                            {% for name, filter in filters %}
                                {% if filter.type == 'text' %}
                                    <div class="mb-3">
                                        <input type="text" name="{{ name }}" value="{{ filter.values }}" placeholder="{{ filter.label }}" class="form-control">
                                    </div>
                                {% elseif filter.type == 'select' %}
                                    <div class="mb-3">
                                        <select name="{{ name }}" placeholder="{{ filter.label }}" class="form-control">
                                            {% for option in filter.values %}
                                            <option value="{{ option.id }}" {% if option.selected %} selected {% endif %}>{{ option.label }}</option>
                                            {% endfor %}
                                        </select>
                                    </div>
                                {% endif %}
                            {% endfor %}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify"></i>
                                    {{ phrase('Search') }}
                                </button>
                            </div>
                        </form>
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

    public function pagination($type = null)
    {
        $component = <<<EOF
        <div class="row align-items-center">
            <div class="col-sm-6 text-center text-sm-start">
                <div class="text-muted mb-0 text-sm">
                    <i class="mdi mdi-information-outline"></i>
                    &nbsp;
                    {{ information }}
                </div>
            </div>
            <div class="col-sm-6">
                <form action="{{ action }}" method="GET" class="form-horizontal">
                    {% for input in filters.hidden %}
                        <input type="hidden" name="{{ input.name }}" value="{{ input.value }}">
                    {% endfor %}
                    <div class="float-sm-end pagination">
                        <ul class="pagination pagination-sm mb-0 me-1">
                            {% for link in links %}
                                <li class="{{ link.parent_class }}">
                                    <a href="{{ link.href }}" class="{{ link.class }}">
                                        {{ link.label | raw }}
                                    </a>
                                </li>
                            {% endfor %}
                        </ul>
                        <div class="input-group input-group-sm">
                            {% for input in filters.select %}
                                <select name="{{ input.name }}" class="form-control">
                                    {% for option in input.values %}
                                        <option value="{{ option.value }}"{{ option.selected ? ' selected' : '' }}>{{ option.label }}</option>
                                    {% endfor %}
                                </select>
                            {% endfor %}
                            {% for input in filters.number %}
                                <input type="number" name="{{ input.name }}" value="{{ input.value }}" min="{{ input.min }}" max="{{ input.max }}" class="form-control">
                            {% endfor %}
                            <button type="submit" class="btn btn-primary">
                                OK
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function form($type = null)
    {
        $component = <<<EOF
        <div class="py-3">
            <div class="container-fluid">
                <form action="{{ links.current_page }}" method="POST" class="--validate-form" enctype="multipart/form-data">
                    {% for name, params in results.field_data %}
                        {% if params.type == 'geospatial' %}
                            {# Include form input component #}
                            {% include 'core/form_input.twig' with params %}
                        {% endif %}
                    {% endfor %}
                    <div class="row">
                        <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
                            <div class="row">
                                {# Find index within column total #}
                                {% for index in 1..results.column_total %}
                                    <div class="col {{ results.column_size[index] }}">
                                        {# Loop field data for matching column position by index #}
                                        {% for name, params in results.field_data %}
                                            {% if index == params.position and params.type != 'geospatial' %}
                                                {% if results.set_heading[name] %}
                                                    <h5> {{ results.set_heading[name] }} </h5>
                                                {% endif %}
                                                {% if results.merged_field[name] %}
                                                    <div class="row">
                                                        <div class="col {{ results.field_size[name] }}">
                                                            {# Include form input component #}
                                                            {% include 'core/form_input.twig' with params %}
                                                        </div>

                                                        {% for merged_field in results.merged_field[name] %}
                                                            {% if results.field_data[merged_field] %}
                                                                <div class="col {{ results.field_size[merged_field] }}">
                                                                    {# Include form input component #}
                                                                    {% include 'core/form_input.twig' with {params: results.field_data[merged_field]} %}
                                                                </div>
                                                            {% endif %}
                                                        {% endfor %}
                                                    </div>
                                                {% elseif not params.merged %}
                                                    {# Include form input component #}
                                                    {% include 'core/form_input.twig' with params %}
                                                {% endif %}
                                            {% endif %}
                                        {% endfor %}
                                    </div>
                                {% endfor %}
                            </div>
                            <div role="validation-callback"></div>
                        </div>
                    </div>
                    <div class="opt-btn-overlap-fix"></div>
                    <div class="row opt-btn">
                        <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
                            <a href="{{ links.current_module }}" class="btn btn-link --xhr">
                                <i class="mdi mdi-arrow-left"></i>
                                {{ phrase('Back') }}
                            </a>
                            
                            <button type="submit" class="btn btn-primary float-end">
                                <i class="mdi mdi-check"></i>
                                {{ phrase('Submit') }}
                                <em class="text-sm d-none d-lg-inline">(ctrl+s)</em>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function form_modal($type = null)
    {
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <form action="{{ links.current_page }}" method="POST" class="--validate-form modal-content {% if modal %} border shadow {% endif %}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i> 
                            <span class="modal-title-text">{{ meta.title }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        {% if meta.description %}
                            <div class="pb-3 mb-3">
                                {{ meta.description }}
                            </div>
                        {% endif %}
                        {% for name, params in results.field_data %}
                            {% if params.type == 'geospatial' %}
                                {# Include form input component #}
                                {% include 'core/form_input.twig' with params %}
                            {% endif %}
                        {% endfor %}
                        <div class="row">
                            {# Find index within column total #}
                            {% for index in 1..results.column_total %}
                                <div class="col {{ results.column_size[index] }}">
                                    {# Loop field data for matching column position by index #}
                                    {% for name, params in results.field_data %}
                                        {% if index == params.position and params.type != 'geospatial' %}
                                            {% if results.set_heading[name] %}
                                                <h5> {{ results.set_heading[name] }} </h5>
                                            {% endif %}
                                            {% if results.merged_field[name] %}
                                                <div class="row">
                                                    <div class="col {{ results.field_size[name] }}">
                                                        {# Include form input component #}
                                                        {% include 'core/form_input.twig' with params %}
                                                    </div>

                                                    {% for merged_field in results.merged_field[name] %}
                                                        {% if results.field_data[merged_field] %}
                                                            <div class="col {{ results.field_size[merged_field] }}">
                                                                {# Include form input component #}
                                                                {% include 'core/form_input.twig' with {params: results.field_data[merged_field]} %}
                                                            </div>
                                                        {% endif %}
                                                    {% endfor %}
                                                </div>
                                            {% elseif not params.merged %}
                                                {# Include form input component #}
                                                {% include 'core/form_input.twig' with params %}
                                            {% endif %}
                                        {% endif %}
                                    {% endfor %}
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">
                            {{ phrase('Cancel') }}
                            <em class="text-sm d-none d-lg-inline">(esc)</em>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check"></i> 
                            {{ phrase('Submit') }}
                            <em class="text-sm d-none d-lg-inline">(ctrl+s)</em>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function read($type = null)
    {
        $component = <<<EOF
        <div class="py-3">
            <div class="container-fluid">
                {% for name, params in results.field_data %}
                    {% if params.type == 'geospatial' %}
                        {# Include form read component #}
                        {% include 'core/form_read.twig' with params %}
                    {% endif %}
                {% endfor %}
                <div class="row">
                    <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
                        <div class="row">
                            {# Find index within column total #}
                            {% for index in 1..results.column_total %}
                                <div class="col {{ results.column_size[index] }}">
                                    {# Loop field data for matching column position by index #}
                                    {% for name, params in results.field_data %}
                                        {% if index == params.position and params.type != 'geospatial' %}
                                            {% if results.set_heading[name] %}
                                                <h5> {{ results.set_heading[name] }} </h5>
                                            {% endif %}
                                            {% if results.merged_field[name] %}
                                                <div class="row">
                                                    <div class="col {{ results.field_size[name] }}">
                                                        {# Include form read component #}
                                                        {% include 'core/form_read.twig' with params %}
                                                    </div>

                                                    {% for merged_field in results.merged_field[name] %}
                                                        {% if results.field_data[merged_field] %}
                                                            <div class="col {{ results.field_size[merged_field] }}">
                                                                {# Include form read component #}
                                                                {% include 'core/form_read.twig' with {params: results.field_data[merged_field]} %}
                                                            </div>
                                                        {% endif %}
                                                    {% endfor %}
                                                </div>
                                            {% elseif not params.merged %}
                                                {# Include form read component #}
                                                {% include 'core/form_read.twig' with params %}
                                            {% endif %}
                                        {% endif %}
                                    {% endfor %}
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                </div>
                <div class="opt-btn-overlap-fix"></div>
                <div class="row opt-btn">
                    <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
                        <a href="{{ links.current_module }}" class="btn btn-link --xhr">
                            <i class="mdi mdi-arrow-left"></i>
                            {{ phrase('Back') }}
                        </a>
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

    public function read_modal($type = null)
    {
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <div class="modal-content {% if modal %} border shadow {% endif %}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i> 
                            <span class="modal-title-text">{{ meta.title }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        {% if meta.description %}
                            <div class="pb-3 mb-3">
                                {{ meta.description }}
                            </div>
                        {% endif %}
                        {% for name, params in results.field_data %}
                            {% if params.type == 'geospatial' %}
                                {# Include form read component #}
                                {% include 'core/form_read.twig' with params %}
                            {% endif %}
                        {% endfor %}
                        <div class="row">
                            {# Find index within column total #}
                            {% for index in 1..results.column_total %}
                                <div class="col {{ results.column_size[index] }}">
                                    {# Loop field data for matching column position by index #}
                                    {% for name, params in results.field_data %}
                                        {% if index == params.position and params.type != 'geospatial' %}
                                            {% if results.set_heading[name] %}
                                                <h5> {{ results.set_heading[name] }} </h5>
                                            {% endif %}
                                            {% if results.merged_field[name] %}
                                                <div class="row">
                                                    <div class="col {{ results.field_size[name] }}">
                                                        {# Include form read component #}
                                                        {% include 'core/form_read.twig' with params %}
                                                    </div>

                                                    {% for merged_field in results.merged_field[name] %}
                                                        {% if results.field_data[merged_field] %}
                                                            <div class="col {{ results.field_size[merged_field] }}">
                                                                {# Include form read component #}
                                                                {% include 'core/form_read.twig' with {params: results.field_data[merged_field]} %}
                                                            </div>
                                                        {% endif %}
                                                    {% endfor %}
                                                </div>
                                            {% elseif not params.merged %}
                                                {# Include form read component #}
                                                {% include 'core/form_read.twig' with params %}
                                            {% endif %}
                                        {% endif %}
                                    {% endfor %}
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">
                            {{ phrase('Close') }}
                            <em class="text-sm d-none d-lg-inline">(esc)</em>
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

    public function form_input($type = null)
    {
        $component = <<<EOF
        <div class="mb-3">
            {% if params.label and params.type != 'geospatial' %}
                <label class="form-label text-muted mb-0" for="{{ params.name }}_input">
                    {{ params.label }}
                    {% if params.tooltip %}
                        <i class="mdi mdi-information-outline text-info" data-bs-toggle="tooltip" title="{{ params.tooltip }}"></i>
                    {% endif %}
                    {% if params.required %}
                        <span class="text-danger font-weight-bold">*</span>
                    {% endif %}
                </label>
            {% endif %}
            <div class="input-group">
                {% if params.prepend %}
                    <span class="input-group-text"> {{ params.prepend | raw }} </span>
                {% endif %}

                {# Include form component #}
                {% include 'form/' ~ params.type ~ '.twig' with params %}
                
                {% if params.append %}
                    <span class="input-group-text"> {{ params.append | raw }} </span>
                {% endif %}
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function form_read($type = null)
    {
        $component = <<<EOF
        <div class="mb-3">
            {% if params.label and params.type != 'geospatial' %}
                <label class="form-label text-muted mb-0">
                    {{ params.label }}
                    {% if params.tooltip %}
                        <i class="mdi mdi-information-outline text-info" data-bs-toggle="tooltip" title="{{ params.tooltip }}"></i>
                    {% endif %}
                    {% if params.required %}
                        <span class="text-danger font-weight-bold">*</span>
                    {% endif %}
                </label>
            {% endif %}
            <div class="input-group">
                {% if params.prepend %}
                    <span class="input-group-text-unformatted me-2"> {{ params.prepend | raw }} </span>
                {% endif %}

                {# Include form component #}
                {% include 'view/' ~ params.type ~ '.twig' with params %}
                
                {% if params.append %}
                    <span class="input-group-text-unformatted ms-2"> {{ params.append | raw }} </span>
                {% endif %}
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    public function modal($type = null)
    {
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i> 
                            <span class="modal-title-text">
                            {{ meta.title ?? phrase('Loading') }}
                            </span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        {% if content %}
                            {{ content }}
                        {% else %}
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden sr-only">
                                        {{ phrase('Loading') }}
                                    </span>
                                </div>
                            </div>
                        {% endif %}
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

    public function exception($type = null)
    {
        $component = <<<EOF
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast align-items-center text-bg-{{ color }} fade show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body">
                    <button type="button" class="btn-close me-2 m-auto float-end" data-bs-dismiss="toast" aria-label="{{ phrase('Close') }}"></button>
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="{{ icon }} mdi-2x"></i>
                        </div>
                        <div class="col-10 text-break">
                            {{ message }}
                        </div>
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

    public function error($type = null)
    {
        $component = <<<EOF
        <div class="container-fluid">
            <div class="row bg-dark full-height">
                <div class="py-3 font-monospace">
                    <p class="mb-0 text-success">
                        [info@localhost ~]# aksara trace -exception
                    </p>
                    <p class="text-danger">
                        {{ phrase('No response could be loaded') }}. 
                        {{ phrase('Make sure to check the following mistake') }}:
                    </p>
                    <ol>
                        <li class="text-danger">
                            {{ phrase('Module structure') }},
                        </li>
                        <li class="text-danger">
                            {{ phrase('Incorrect view path') }},
                        </li>
                        <li class="text-danger">
                            {{ phrase('Database table existence') }},
                        </li>
                        <li class="text-danger">
                            {{ phrase('Something caused by typo') }}
                        </li>
                    </ol>
                    <p class="mb-0 text-success">
                        [info@localhost ~]# <blink>_</blink>
                    </p>
                </div>
            </div>
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }
}
