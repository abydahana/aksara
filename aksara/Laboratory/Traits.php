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

namespace Aksara\Laboratory;

/**
 * This trait's contains default dynamic properties
 */
trait Traits
{
    private $_add_class = [];

    private $_add_button = [];

    private $_add_dropdown = [];

    private $_add_filter = [];

    private $_add_toolbar = [];

    private $_after_delete;

    private $_after_insert;

    private $_after_update;

    private $_before_delete;

    private $_before_insert;

    private $_before_update;

    private $_cloning;

    private $_column_order = [];

    private $_column_size = [];

    private $_compiled_select = [];

    private $_compiled_table = [];

    private $_data = [];

    private $_db_driver;

    private $_debugging;

    private $_default_value = [];

    private $_distinct;

    private $_extra_dropdown = [];

    private $_extra_option = [];

    private $_extra_submit = [];

    private $_extra_toolbar = [];

    private $_field_append = [];

    private $_field_order = [];

    private $_field_position = [];

    private $_field_prepend = [];

    private $_field_size = [];

    private $_form_callback;

    private $_grid_view;

    private $_group_field = [];

    private $_insert_id;

    private $_insert_on_update_fail;

    private $_item_reference = [];

    private $_join = [];

    private $_language;

    private $_like = [];

    private $_limit = 25;

    private $_limit_backup = 25;

    private $_merge_content = [];

    private $_merge_field = [];

    private $_merge_label = [];

    private $_method;

    private $_modal_size;

    private $_module;

    private $_offset;

    private $_old_files;

    private $_output = [];

    private $_parameter = [];

    private $_prepare = [];

    private $_query;

    private $_redirect_back;

    private $_restrict_on_demo;

    private $_results = [];

    private $_searchable = true;

    private $_select = [];

    private $_set_alias = [];

    private $_set_attribute = [];

    private $_set_autocomplete = [];

    private $_set_breadcrumb = [];

    private $_set_default = [];

    private $_set_description = [];

    private $_set_description_fallback;

    private $_set_field = [];

    private $_set_heading = [];

    private $_set_icon = [];

    private $_set_icon_fallback;

    private $_set_messages = [];

    private $_set_method = [];

    private $_set_option_label = [];

    private $_set_output = [];

    private $_set_permission;

    private $_set_placeholder = [];

    private $_set_primary = [];

    private $_set_relation = [];

    private $_set_template = [];

    private $_set_theme;

    private $_set_title = [];

    private $_set_title_fallback;

    private $_set_tooltip = [];

    private $_set_upload_path;

    private $_set_validation = [];

    private $_table;

    private $_total;

    private $_translate_field = [];

    private $_uploaded_files = [];

    private $_unset_clone = [];

    private $_unset_column = [];

    private $_unset_delete = [];

    private $_unset_field = [];

    private $_unset_method = [];

    private $_unset_read = [];

    private $_unset_select = [];

    private $_unset_truncate = [];

    private $_unset_update = [];

    private $_unset_view = [];

    private $_view = 'index';

    private $_view_order = [];

    private $_where = [];
}
