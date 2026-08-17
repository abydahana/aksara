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
 * Core Component Builder
 *
 * This class contains raw Twig templates used to build the primary UI components
 * of the CMS, such as data tables, forms, grids, toolbars, and modals.
 */
class Core
{
    /**
     * Generate Table View Component.
     * Renders a standard data grid (table) with checkboxes, column sorting,
     * action buttons, and dynamic data rows.
     */
    public function index(): array
    {
        // Template for Table View
        // Includes: Toolbar, Bulk Delete Checkbox, Sortable Headers, Data Loop, and Pagination
        $component = <<<EOF
        <div data-role="toolbar" class="alias-table-toolbar p-2 border-bottom">
            {# Include toolbar component #}
            {% include 'core/toolbar.twig' with results.toolbar %}
        </div>
        <div data-role="table" class="table-responsive alias-table-index px-2">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th width="1">
                            <div class="mb-0">
                                <input type="checkbox" class="form-check-input bulk-delete" data-bs-toggle="tooltip" title="{{ phrase('Check All') }}" aria-label="{{ phrase('Check All') }}" data-role="checker" data-parent="table">
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
                                    <a href="{{ column.url }}" class="fw-bold --xhr {{ column.class }}">
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
                <tbody {{ results.sortable ? 'data-role="sortable" data-url="' ~ results.sortable.sort_url ~ '"' : 'data-role="tbody"' }}>
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
                        <tr id="row_{{ key }}" data-id="{{ row.primary[results.sortable.primary_key] }}">
                            <td>
                                {% if row.deleting %}
                                    <div class="mb-0">
                                        <input type="checkbox" name="bulk_delete[]" value="{{ row.primary | json_encode | escape }}" class="form-check-input" aria-label="{{ phrase('Select Row') }}">
                                    </div>
                                {% endif %}
                            </td>
                            <td>
                                <div class="btn-group btn-group-xs">
                                    {% for button in row.buttons %}
                                        <a href="{{ button.url }}" class="btn {{ button.class }}" data-bs-toggle="tooltip" title="{{ button.label }}" aria-label="{{ button.label }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                            <i class="{{ button.icon }}"></i>
                                        </a>
                                    {% endfor %}
                                    {% if row.dropdowns | length > 0 %}
                                        <a href="{{ current_page() }}" class="btn btn-secondary --open-item-option" data-bs-toggle="tooltip" title="{{ phrase('More options') }}" aria-label="{{ phrase('More options') }}" data-options="{{ row.dropdowns | json_encode | escape }}">
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
                    {% else %}
                        <tr class="no-hover">
                            <td colspan="{{ colspan + 2 }}">
                                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="min-height:50vh">
                                    <div class="mb-3">
                                        <i class="mdi mdi-emoticon-sad-outline mdi-5x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted mb-0">
                                        {{ phrase('No data found') }}
                                    </h5>
                                </div>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
        <div data-role="pagination" class="alias-pagination border-top py-2 px-2">
            {# Include pagination component #}
            {% include 'core/pagination.twig' with pagination %}
        </div>
        EOF;

        return [
            'type' => __FUNCTION__,
            'component' => $component
        ];
    }

    /**
     * Generate Mobile View Component.
     * Renders a simplified card-based layout optimized for mobile screens.
     */
    public function index_mobile(): array
    {
        // Template for Mobile View
        // Includes: Card Grid, Pagination, and Bottom Toolbar
        $component = <<<EOF
        <div data-role="grid" class="pt-3">
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
                                                {% for carouselKey, carouselItem in field.content %}
                                                    <div class="carousel-item position-relative rounded-4 {% if carouselKey is same as(0) %} active {% endif %}">
                                                        <a href="{{ carouselItem.url }}" target="_blank">
                                                            <div class="clip gradient-top rounded-top"></div>
                                                            <img src="{{ carouselItem.thumbnail }}" class="d-block rounded w-100" alt="{{ carouselItem.label ? carouselItem.label : phrase('Image') }}" loading="lazy" decoding="async">
                                                        </a>
                                                    </div>
                                                {% endfor %}
                                            </div>
                                            <a class="carousel-control-prev gradient-right" href="#slideshow_{{ key }}" role="button" data-bs-slide="prev" aria-label="{{ phrase('Previous') }}">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            </a>
                                            <a class="carousel-control-next gradient-left" href="#slideshow_{{ key }}" role="button" data-bs-slide="next" aria-label="{{ phrase('Next') }}">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            </a>
                                        </div>

                                        {% set break = true %}
                                    {% elseif not break and field.type == 'image' %}
                                        <a href="{{ field.content | replace({'/thumbs/': '/'}) }}" target="_blank">
                                            <img src="{{ field.content }}" class="d-block rounded w-100" alt="{{ field.label ? field.label : phrase('Image') }}" loading="lazy" decoding="async">
                                        </a>

                                        {% set break = true %}
                                    {% endif %}
                                {% endfor %}
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        {% for field in row.field_data %}
                                            {% if field.type != 'image' and field.type != 'images' %}
                                                <li class="list-group-item px-0">
                                                    <span class="text-sm text-muted d-block">{{ field.label }}</span>
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
                                            <a href="{{ current_page() }}" class="btn btn-secondary --open-item-option" data-bs-toggle="tooltip" title="{{ phrase('More options') }}" data-options="{{ row.dropdowns | json_encode | escape }}">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                            </a>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% else %}
                        <div class="col-12">
                            <div class="d-flex flex-column align-items-center justify-content-center text-center py-5 my-5" style="min-height: 50vh;">
                                <div class="mb-3">
                                    <i class="mdi mdi-emoticon-sad-outline mdi-5x text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-0">
                                    {{ phrase('No data found') }}
                                </h5>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        {% if results.table_data | length > 0 or pagination.page > 1 %}
            <div data-role="pagination" class="alias-pagination pb-3">
                <div class="container-fluid">
                    {# Include pagination component #}
                    {% include 'core/pagination.twig' with pagination %}
                </div>
            </div>
        {% endif %}
        <div data-role="toolbar" class="alias-table-toolbar py-1">
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

    /**
     * Generate Grid View Component.
     * Renders a card-based layout, useful for galleries or blogs.
     */
    public function index_grid(): array
    {
        // Template for Grid View
        // Includes: Toolbar, Card Loop (Bootstrap Grid), Image Carousel logic, and Pagination
        $component = <<<EOF
        <div data-role="toolbar" class="alias-table-toolbar py-2 border-bottom">
            <div class="container-fluid">
                {# Include toolbar component #}
                {% include 'core/toolbar.twig' with results.toolbar %}
            </div>
        </div>
        <div data-role="grid">
            <div class="p-3">
                {% set use_horizontal_card = false %}
                {% if results.table_data | length > 0 %}
                    {% set first_row_has_image = false %}
                    {% set first_row_non_image_count = 0 %}
                    {% for field in results.table_data[0].field_data %}
                        {% if field.type == 'image' or field.type == 'images' %}
                            {% set first_row_has_image = true %}
                        {% else %}
                            {% set first_row_non_image_count = first_row_non_image_count + 1 %}
                        {% endif %}
                    {% endfor %}
                    {% if first_row_has_image and first_row_non_image_count > 3 %}
                        {% set use_horizontal_card = true %}
                    {% endif %}
                {% endif %}

                <div class="row row-gap-4">
                    {% for key, row in results.table_data %}
                        {% if use_horizontal_card %}
                            {# Include horizontal card component #}
                            {% include 'core/card_horizontal.twig' with {row: row, key: key} %}
                        {% else %}
                            {# Include vertical card component #}
                            {% include 'core/card_vertical.twig' with {row: row, key: key} %}
                        {% endif %}
                    {% else %}
                        <div class="col-12">
                            <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="min-height:50vh">
                                <div class="mb-3">
                                    <i class="mdi mdi-emoticon-sad-outline mdi-5x text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-0">
                                    {{ phrase('No data found') }}
                                </h5>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        <div data-role="pagination" class="alias-pagination py-2 border-top">
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

    /**
     * Generate Horizontal Card Component.
     * 2-column card layout with image on left and fields on right.
     */
    public function card_horizontal(): array
    {
        $component = <<<EOF
        {# 2-column horizontal card (Image on left, Fields on right). Grid per row: max 2 cards (col-12 col-lg-6) #}
        <div class="col-12 col-lg-6">
            <div class="card border border-hover rounded-4 overflow-hidden h-100 position-relative">
                {% if row.buttons | length > 0 or row.dropdowns | length > 0 %}
                    <div class="position-absolute top-0 end-0 p-2 z-1">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-light border-0 rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm" style="width: 32px; height: 32px;" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ phrase('Options') }}">
                                <i class="mdi mdi-dots-vertical mdi-18px"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                {% for button in row.buttons %}
                                    {% set clean_class = button.class
                                        | replace({'btn-primary': '', 'btn-secondary': '', 'btn-success': '', 'btn-danger': 'text-danger', 'btn-warning': '', 'btn-info': '', 'btn-light': '', 'btn-dark': '', 'btn-link': ''})
                                        | replace({'btn-outline-primary': '', 'btn-outline-secondary': '', 'btn-outline-success': '', 'btn-outline-danger': 'text-danger', 'btn-outline-warning': '', 'btn-outline-info': '', 'btn-outline-light': '', 'btn-outline-dark': ''})
                                        | replace({'btn-sm': '', 'btn-lg': '', 'btn-xs': '', 'btn': ''})
                                    %}
                                    <li>
                                        <a href="{{ button.url }}" class="dropdown-item d-flex align-items-center py-2 px-3 {{ clean_class }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                            <i class="{{ button.icon }} me-2 text-muted"></i>
                                            <span>{{ button.label }}</span>
                                        </a>
                                    </li>
                                {% endfor %}
                                {% if row.dropdowns | length > 0 %}
                                    {% if row.buttons | length > 0 %}
                                        <li><hr class="dropdown-divider my-1"></li>
                                    {% endif %}
                                    <li>
                                        <a href="{{ current_page() }}" class="dropdown-item d-flex align-items-center py-2 px-3 --open-item-option" data-options="{{ row.dropdowns | json_encode | escape }}">
                                            <i class="mdi mdi-format-list-bulleted me-2 text-muted"></i>
                                            <span>{{ phrase('More options') }}</span>
                                        </a>
                                    </li>
                                {% endif %}
                            </ul>
                        </div>
                    </div>
                {% endif %}

                <div class="row g-0 h-100">
                    <div class="col-4 col-sm-3 col-md-4 d-flex align-items-center justify-content-center p-3">
                        {% set break = false %}
                        {% for field in row.field_data %}
                            {% if not break and field.type == 'images' %}
                                <div id="slideshow_{{ key }}" class="carousel slide w-100" data-bs-ride="carousel">
                                    <div class="carousel-inner rounded-3 overflow-hidden">
                                        {% for carouselKey, carouselItem in field.content %}
                                            <div class="carousel-item position-relative {% if carouselKey is same as(0) %} active {% endif %}">
                                                <a href="{{ carouselItem.url }}" target="_blank" class="d-block">
                                                    <img src="{{ carouselItem.thumbnail }}" class="d-block w-100 rounded-4" alt="{{ carouselItem.label ? carouselItem.label : phrase('Image') }}" loading="lazy" decoding="async">
                                                </a>
                                            </div>
                                        {% endfor %}
                                    </div>
                                    {% if field.content | length > 1 %}
                                        <a class="carousel-control-prev gradient-right" href="#slideshow_{{ key }}" role="button" data-bs-slide="prev" aria-label="{{ phrase('Previous') }}">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        </a>
                                        <a class="carousel-control-next gradient-left" href="#slideshow_{{ key }}" role="button" data-bs-slide="next" aria-label="{{ phrase('Next') }}">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        </a>
                                    {% endif %}
                                </div>

                                {% set break = true %}
                            {% elseif not break and field.type == 'image' %}
                                <a href="{{ field.content | replace({'/thumbs/': '/'}) }}" target="_blank" class="d-block text-center w-100">
                                    <img src="{{ field.content }}" class="img-fluid rounded-3" alt="{{ field.label ? field.label : phrase('Image') }}" loading="lazy" decoding="async">
                                </a>

                                {% set break = true %}
                            {% endif %}
                        {% endfor %}
                    </div>
                    <div class="col-8 col-sm-9 col-md-8 p-3 pe-5">
                        <ul class="list-group list-group-flush mb-0">
                            {% for field in row.field_data %}
                                {% if field.type != 'image' and field.type != 'images' %}
                                    <li class="list-group-item px-0 py-1 border-0">
                                        <span class="text-xs text-muted d-block">{{ field.label }}</span>
                                        {# Include table component #}
                                        {% include 'table/' ~ field.type ~ '.twig' with field %}
                                    </li>
                                {% endif %}
                            {% endfor %}
                        </ul>
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
     * Generate Vertical Card Component.
     * 1-column card layout with image on top and fields below.
     */
    public function card_vertical(): array
    {
        $component = <<<EOF
        {# Standard 1-column card (Image on top, Fields below). Grid per row: max 4 cards (col-sm-6 col-md-4 col-xl-3) #}
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card border border-hover rounded-4 overflow-hidden h-100 position-relative">
                {% if row.buttons | length > 0 or row.dropdowns | length > 0 %}
                    <div class="position-absolute top-0 end-0 p-2 z-1">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-light border-0 rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm" style="width: 32px; height: 32px;" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ phrase('Options') }}">
                                <i class="mdi mdi-dots-vertical mdi-18px"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                {% for button in row.buttons %}
                                    {% set clean_class = button.class
                                        | replace({'btn-primary': '', 'btn-secondary': '', 'btn-success': '', 'btn-danger': 'text-danger', 'btn-warning': '', 'btn-info': '', 'btn-light': '', 'btn-dark': '', 'btn-link': ''})
                                        | replace({'btn-outline-primary': '', 'btn-outline-secondary': '', 'btn-outline-success': '', 'btn-outline-danger': 'text-danger', 'btn-outline-warning': '', 'btn-outline-info': '', 'btn-outline-light': '', 'btn-outline-dark': ''})
                                        | replace({'btn-sm': '', 'btn-lg': '', 'btn-xs': '', 'btn': ''})
                                    %}
                                    <li>
                                        <a href="{{ button.url }}" class="dropdown-item d-flex align-items-center py-2 px-3 {{ clean_class }}" {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                                            <i class="{{ button.icon }} me-2 text-muted"></i>
                                            <span>{{ button.label }}</span>
                                        </a>
                                    </li>
                                {% endfor %}
                                {% if row.dropdowns | length > 0 %}
                                    {% if row.buttons | length > 0 %}
                                        <li><hr class="dropdown-divider my-1"></li>
                                    {% endif %}
                                    <li>
                                        <a href="{{ current_page() }}" class="dropdown-item d-flex align-items-center py-2 px-3 --open-item-option" data-options="{{ row.dropdowns | json_encode | escape }}">
                                            <i class="mdi mdi-format-list-bulleted me-2 text-muted"></i>
                                            <span>{{ phrase('More options') }}</span>
                                        </a>
                                    </li>
                                {% endif %}
                            </ul>
                        </div>
                    </div>
                {% endif %}

                {% set break = false %}
                {% for field in row.field_data %}
                    {% if not break and field.type == 'images' %}
                        <div id="slideshow_{{ key }}" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                {% for carouselKey, carouselItem in field.content %}
                                    <div class="carousel-item position-relative rounded-4 {% if carouselKey is same as(0) %} active {% endif %}">
                                        <a href="{{ carouselItem.url }}" target="_blank">
                                            <div class="clip gradient-top rounded-top"></div>
                                            <img src="{{ carouselItem.thumbnail }}" class="d-block rounded-4 w-100" alt="{{ carouselItem.label ? carouselItem.label : phrase('Image') }}" loading="lazy" decoding="async">
                                        </a>
                                    </div>
                                {% endfor %}
                            </div>
                            <a class="carousel-control-prev gradient-right" href="#slideshow_{{ key }}" role="button" data-bs-slide="prev" aria-label="{{ phrase('Previous') }}">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next gradient-left" href="#slideshow_{{ key }}" role="button" data-bs-slide="next" aria-label="{{ phrase('Next') }}">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>
                        </div>

                        {% set break = true %}
                    {% elseif not break and field.type == 'image' %}
                        <a href="{{ field.content | replace({'/thumbs/': '/'}) }}" target="_blank">
                            <img src="{{ field.content }}" class="d-block rounded w-100" alt="{{ field.label ? field.label : phrase('Image') }}" loading="lazy" decoding="async">
                        </a>

                        {% set break = true %}
                    {% endif %}
                {% endfor %}
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {% for field in row.field_data %}
                            {% if field.type != 'image' and field.type != 'images' %}
                                <li class="list-group-item px-0">
                                    <span class="text-sm text-muted d-block">{{ field.label }}</span>
                                    {# Include table component #}
                                    {% include 'table/' ~ field.type ~ '.twig' with field %}
                                </li>
                            {% endif %}
                        {% endfor %}
                    </ul>
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
     * Generate Toolbar Component.
     * Renders the top action buttons (Create, Export, Print) and the
     * search/filter inputs (Text search, Dropdowns).
     */
    public function toolbar(): array
    {
        // Template for Desktop Toolbar
        // Includes: Action Buttons Group and Filter Form (Input/Select)
        $component = <<<EOF
        <div class="row">
            <div class="col">
                <div class="btn-group btn-group-sm">
                    {% for key, button in buttons %}
                        <a href="{{ button.url }}" class="btn {{ button.class }}" data-bs-toggle="tooltip" title="{{ button.label }}" aria-label="{{ button.label }}" {% if button.path == 'delete' %} data-bulk-delete="true" {% endif %} {% if button.new_tab %} target="_blank" {% endif %} {{ button.attribution | raw }}>
                            <i class="{{ button.icon }}"></i>
                            {% if button.path != 'delete' %} {{ button.label }} {% endif %}
                        </a>
                    {% endfor %}
                </div>
            </div>
            <div class="col">
                <form action="{{ action }}" method="GET" class="form-horizontal filter-form">
                    <div class="input-group input-group-sm">
                        {% for name, filter in filters %}
                            {% if filter.type == 'text' %}
                                <input type="text" name="{{ name }}" value="{{ filter.values }}" placeholder="{{ filter.label }}" aria-label="{{ filter.label }}" class="form-control">
                            {% elseif filter.type == 'select' %}
                                <select name="{{ name }}" placeholder="{{ filter.label }}" aria-label="{{ filter.label }}" class="form-control">
                                    {% for option in filter.values %}
                                    <option value="{{ option.id }}" {% if option.selected %} selected {% endif %}>{{ option.label }}</option>
                                    {% endfor %}
                                </select>
                            {% endif %}
                        {% endfor %}
                        <button type="submit" class="btn btn-primary" aria-label="{{ phrase('Search') }}">
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

    /**
     * Generate Mobile Toolbar Component.
     * Renders a sticky action bar and a modal for searching/filtering data.
     */
    public function toolbar_mobile(): array
    {
        // Template for Mobile Toolbar
        // Includes: Sticky Bottom Buttons (limited to 3, rest in 'More') and Search Modal
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
                    <a href="{{ current_page() }}" class="btn btn-secondary --open-item-option" data-options="{{ buttons | slice(3) | json_encode | escape }}">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        {{ phrase('More') }}
                    </a>
                {% endif %}
            {% endfor %}
        </div>
        <div class="modal --prevent-remove" id="searchModal" tabindex="-1" aria-labelledby="searchModalCenterTitle" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered ui-draggable" role="document">
                <div class="modal-content border-hover rounded-4">
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

    /**
     * Generate Pagination Component.
     * Renders page links, "results info", and per-page limits selector.
     */
    public function pagination(): array
    {
        // Template for Pagination
        // Includes: Page Links, Info Text, and Hidden Inputs for persistent filtering
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
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-2">
                        <ul class="pagination pagination-sm mb-0">
                            {% for link in links %}
                                <li class="{{ link.parent_class }}">
                                    <a href="{{ link.href }}" class="{{ link.class }}">
                                        {{ link.label | raw }}
                                    </a>
                                </li>
                            {% endfor %}
                        </ul>
                        <div class="input-group input-group-sm w-auto">
                            {% for input in filters.select %}
                                <select name="{{ input.name }}" class="form-control" aria-label="{{ phrase('Per Page') }}">
                                    {% for option in input.values %}
                                        <option value="{{ option.value }}"{{ option.selected ? ' selected' : '' }}>{{ option.label }}</option>
                                    {% endfor %}
                                </select>
                            {% endfor %}
                            {% for input in filters.number %}
                                <input type="number" name="{{ input.name }}" value="{{ input.value }}" min="{{ input.min }}" max="{{ input.max }}" class="form-control" style="max-width: 65px;" aria-label="{{ phrase('Page Number') }}">
                            {% endfor %}
                            <button type="submit" class="btn btn-primary" aria-label="{{ phrase('Submit') }}">
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

    /**
     * Generate Form View Component.
     * Renders the Create/Update form structure, including field grouping,
     * merging, layout positioning, and form validation attributes.
     */
    public function form(): array
    {
        // Template for CRUD Form
        // Includes: Form Tag, Field Loop (with positioning logic), Merged Fields, and Submit Buttons
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
                        <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 or results.form_size == 'form-lg' %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
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
                            <div data-role="validation-callback"></div>
                        </div>
                    </div>
                    <div class="opt-btn-overlap-fix"></div>
                    <div class="row opt-btn">
                        <div class="col-12 {% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 or results.form_size == 'form-lg' %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %} d-flex justify-content-between align-items-center gap-2">
                            <a href="{{ links.current_module }}" class="btn btn-link --xhr">
                                <i class="mdi mdi-arrow-left"></i>
                                {{ phrase('Back') }}
                            </a>

                            <div class="d-flex justify-content-end align-items-center gap-1">
                                {% for button in results.extra_action.submit %}
                                    <button type="button" name="{{ button.name }}" class="{{ button.class }} me-1" {{ button.attribution | raw }}>
                                        {% if button.icon %}
                                            <i class="{{ button.icon }}"></i>
                                        {% endif %}
                                        {{ button.label }}
                                    </button>
                                {% endfor %}

                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check"></i>
                                    {{ phrase('Submit') }}
                                    <em class="text-sm d-none d-lg-inline">(ctrl+s)</em>
                                </button>
                            </div>
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

    /**
     * Generate Modal Form Component.
     * Renders the Create/Update form structure inside a modal dialog.
     */
    public function form_modal(): array
    {
        // Template for Modal Form
        // Includes: Modal Wrapper, Form Body with field logic, and Modal Footer Actions
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <form action="{{ links.current_page }}" method="POST" class="--validate-form modal-content border-hover rounded-4 {% if modal %} border shadow {% endif %}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i>
                            <span class="modal-title-text">{{ meta.title }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        {% if meta.description %}
                            <div class="mb-3">
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
                        <button type="button" class="btn btn-outline-secondary me-1" data-bs-dismiss="modal">
                            {{ phrase('Cancel') }}
                            <em class="text-sm d-none d-lg-inline">(esc)</em>
                        </button>
                        {% for button in results.extra_action.submit %}
                            <button type="button" name="{{ button.name }}" value="{{ button.value }}" class="{{ button.class }}" {{ button.attribution | raw }}>
                                {% if button.icon %}
                                    <i class="{{ button.icon }}"></i>
                                {% endif %}
                                {{ button.label }}
                            </button>
                        {% endfor %}
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

    /**
     * Generate Read View Component.
     * Renders a read-only detail view of a record, using field layout logic
     * similar to the form view but with static text/displays.
     */
    public function read(): array
    {
        // Template for Read/Detail View
        // Includes: Field Loop (using form_read logic) and Back Button
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
                    <div class="{% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 or results.form_size == 'form-lg' %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %}">
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
                    <div class="col-12 {% if results.column_total > 2 or results.form_size == 'form-xl' %} col-md-12 col-xxl-12 {% elseif results.column_total == 2 or results.form_size == 'form-lg' %} col-md-10 col-xxl-8 {% else %} col-md-6 col-xxl-6 {% endif %} d-flex justify-content-between align-items-center gap-2">
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

    /**
     * Generate Modal Read Component.
     * Renders the read-only detail view inside a modal dialog.
     */
    public function read_modal(): array
    {
        // Template for Modal Read/Detail View
        // Includes: Modal Wrapper and Read-Only Field Loop
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <div class="modal-content border-hover rounded-4 {% if modal %} border shadow {% endif %}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i>
                            <span class="modal-title-text">{{ meta.title }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ phrase('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        {% if meta.description %}
                            <div class="mb-3">
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
                        <button type="button" class="btn btn-outline-secondary me-1" data-bs-dismiss="modal">
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

    /**
     * Generate Form Input Wrapper.
     * Renders a single form input container, including label, tooltip,
     * prepend/append addons, and the actual input field template.
     */
    public function form_input(): array
    {
        // Wrapper template for Input Fields
        // Includes: Label, Tooltip Info, Required Asterisk, Input Group (Prepend/Append)
        $component = <<<EOF
        <div class="mb-3">
            {% if params.label and params.type != 'geospatial' %}
                <label class="form-label text-muted mb-0" for="{{ params.type in ['checkbox', 'radio'] ? params.name ~ '_0_input' : params.name ~ '_input' }}">
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

    /**
     * Generate Read Field Wrapper.
     * Renders a single read-only field container, similar to form_input
     * but optimized for data display (view mode).
     */
    public function form_read(): array
    {
        // Wrapper template for Read-Only Fields
        // Includes: Label and Read-Only Value Display
        $component = <<<EOF
        <div class="mb-3">
            {% if params.label and params.type != 'geospatial' %}
                <span class="form-label text-muted mb-0 d-block">
                    {{ params.label }}
                    {% if params.tooltip %}
                        <i class="mdi mdi-information-outline text-info" data-bs-toggle="tooltip" title="{{ params.tooltip }}"></i>
                    {% endif %}
                    {% if params.required %}
                        <span class="text-danger font-weight-bold">*</span>
                    {% endif %}
                </span>
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

    /**
     * Generate Generic Modal Component.
     * Renders a blank modal container, typically used for loading states
     * or dynamic content injection via AJAX.
     */
    public function modal(): array
    {
        // Template for Generic/Loading Modal
        // Includes: Title with Icon, Close Button, and Spinner (Loader)
        $component = <<<EOF
        <div class="modal" id="dynamic-modal-{{ identifier }}" role="dialog" aria-labelledby="dynamic-modal-{{ identifier }}-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered {{ meta.modal_size }}" role="document">
                <div class="modal-content border-hover rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamic-modal-{{ identifier }}-title">
                            <i class="{{ meta.icon ?? 'mdi mdi-loading mdi-spin' }}"></i>
                            <span class="modal-title-text">
                            {{ meta.title ?? phrase('Loading...') }}
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
                                        {{ phrase('Loading...') }}
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

    /**
     * Generate Exception Toast Component.
     * Renders a floating toast notification for alerts or error messages.
     */
    public function exception(): array
    {
        // Template for Toast Notification
        // Includes: Icon, Message Body, and Color Context
        $component = <<<EOF
        <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
            <div class="toast align-items-center text-bg-{{ color }} fade show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <div class="row align-items-center">
                            <div class="col-2">
                                <i class="{{ icon }} mdi-2x"></i>
                            </div>
                            <div class="col-10 text-break">
                                {{ message }}
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ phrase('Close') }}"></button>
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
     * Generate Full Screen Error Component.
     * Renders a CLI-style error screen for critical failures or debugging.
     */
    public function error(): array
    {
        // Template for Critical Error Screen
        // Includes: Console-like output with troubleshooting steps
        $component = <<<EOF
        <div class="container-fluid bg-dark">
            <div class="row full-height">
                <div class="py-3 font-monospace">
                    <p class="mb-0 text-success">
                        [info@localhost ~]# aksara trace -exception
                    </p>
                    <p class="text-danger">
                        {{ phrase('No response could be loaded.') }} {{ phrase('Make sure to check the following mistake:') }}
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

    /**
     * Generate not found component.
     */
    public function page404(): array
    {
        $component = <<<EOF
        <div class="section-padding">
            <div class="container fade-in">
                <div class="text-center">
                    <h1 class="display-1 lh-1 text-muted">
                        404
                    </h1>
                    <i class="mdi mdi-dropbox display-1 text-muted"></i>
                </div>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h2 class="text-center">
                            {{ phrase('Page not found!') }}
                        </h2>
                        <p class="fs-5 text-center">
                            {{ phrase('The page you requested does not exist or has already been archived.') }}
                        </p>
                        <div class="text-center">
                            <a href="{{ base_url() }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="mdi mdi-arrow-left"></i>
                                {{ phrase('Back to Homepage') }}
                            </a>
                        </div>
                    </div>
                </div>
                {% if suggestions %}
                    <div class="row mb-3">
                        <div class="col-md-8 offset-md-2">
                            <h5>
                                {{ phrase('Our suggestions') }}
                                <span class="blink">_</span>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            {% for index, page in suggestions %}
                                {% if index %} &middot; {% endif %}
                                <a href="{{ links.base_url }}pages/{{ page.page_slug }}" class="fs-5 --xhr">
                                    {{ page.page_title }}
                                </a>
                            {% endfor %}
                        </div>
                    </div>
                {% endif %}
            </div>
        </div>
        EOF;

        return [
            'type' => '404',
            'component' => $component
        ];
    }
}
