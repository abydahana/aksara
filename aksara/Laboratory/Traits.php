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
 * This trait contains default dynamic properties used throughout the
 * core controller to manage CRUD operations, views, and configurations.
 */
trait Traits
{
    /**
     * Container for additional CSS classes.
     * @var array
     */
    private $_add_class = [];

    /**
     * Container for custom buttons.
     * @var array
     */
    private $_add_button = [];

    /**
     * Container for custom dropdown menus.
     * @var array
     */
    private $_add_dropdown = [];

    /**
     * Container for custom filters.
     * @var array
     */
    private $_add_filter = [];

    /**
     * Container for custom toolbar items.
     * @var array
     */
    private $_add_toolbar = [];

    /**
     * Callback to execute after a delete operation.
     * @var callable|null
     */
    private $_after_delete;

    /**
     * Callback to execute after an insert operation.
     * @var callable|null
     */
    private $_after_insert;

    /**
     * Callback to execute after an update operation.
     * @var callable|null
     */
    private $_after_update;

    /**
     * Callback to execute before a delete operation.
     * @var callable|null
     */
    private $_before_delete;

    /**
     * Callback to execute before an insert operation.
     * @var callable|null
     */
    private $_before_insert;

    /**
     * Callback to execute before an update operation.
     * @var callable|null
     */
    private $_before_update;

    /**
     * Cloning mode status.
     * @var bool
     */
    private $_cloning;

    /**
     * Column ordering configuration for tables.
     * @var array
     */
    private $_column_order = [];

    /**
     * Column size configuration.
     * @var array
     */
    private $_column_size = [];

    /**
     * Store the compiled SELECT statement.
     * @var array
     */
    private $_compiled_select = [];

    /**
     * Store the compiled table name.
     * @var array
     */
    private $_compiled_table = [];

    /**
     * Custom formatting status.
     * @var bool
     */
    private $_custom_format = false;

    /**
     * Container for retrieved data.
     * @var array
     */
    private $_data = [];

    /**
     * Database driver name.
     * @var string
     */
    private $_db_driver;

    /**
     * Debugging mode status.
     * @var bool
     */
    private $_debugging;

    /**
     * Default value configuration for form fields.
     * @var array
     */
    private $_default_value = [];

    /**
     * Distinct query status.
     * @var bool
     */
    private $_distinct;

    /**
     * Container for extra dropdown options.
     * @var array
     */
    private $_extra_dropdown = [];

    /**
     * Container for extra options.
     * @var array
     */
    private $_extra_option = [];

    /**
     * Container for extra submit buttons.
     * @var array
     */
    private $_extra_submit = [];

    /**
     * Container for extra toolbar items.
     * @var array
     */
    private $_extra_toolbar = [];

    /**
     * Field suffix configuration (append).
     * @var array
     */
    private $_field_append = [];

    /**
     * Field ordering configuration for forms.
     * @var array
     */
    private $_field_order = [];

    /**
     * Field positioning configuration.
     * @var array
     */
    private $_field_position = [];

    /**
     * Field prefix configuration (prepend).
     * @var array
     */
    private $_field_prepend = [];

    /**
     * Input field size configuration.
     * @var array
     */
    private $_field_size = [];

    /**
     * Callback for form processing.
     * @var callable|null
     */
    private $_form_callback;

    /**
     * Grid view mode status.
     * @var bool
     */
    private $_grid_view;

    /**
     * Field grouping configuration.
     * @var array
     */
    private $_group_field = [];

    /**
     * Flag to skip URL signature validation (HMAC).
     * Use this for public search pages or reports with dynamic GET parameters.
     * @var array
     */
    private $_ignore_query_string = [];

    /**
     * Last inserted ID.
     * @var int|string
     */
    private $_insert_id;

    /**
     * Container for referenced items.
     * @var array
     */
    private $_item_reference = [];

    /**
     * Join clause configuration.
     * @var array
     */
    private $_join = [];

    /**
     * Language configuration.
     * @var string
     */
    private $_language;

    /**
     * Like clause configuration.
     * @var array
     */
    private $_like = [];

    /**
     * Query limit configuration.
     * @var int
     */
    private $_limit = 25;

    /**
     * Backup of query limit configuration.
     * @var int
     */
    private $_limit_backup = 25;

    /**
     * Content merge configuration.
     * @var array
     */
    private $_merge_content = [];

    /**
     * Field merge configuration.
     * @var array
     */
    private $_merge_field = [];

    /**
     * Label merge configuration.
     * @var array
     */
    private $_merge_label = [];

    /**
     * Request method (GET, POST, etc.) or Controller method.
     * @var string
     */
    private $_method;

    /**
     * Modal dialog size configuration.
     * @var string
     */
    private $_modal_size;

    /**
     * Current module name.
     * @var string
     */
    private $_module;

    /**
     * Query offset configuration.
     * @var int
     */
    private $_offset;

    /**
     * Flag to check if offset has been called.
     * @var bool
     */
    private $_offset_called = false;

    /**
     * Container for old files (used during update).
     */
    private $_old_files;

    /**
     * Output buffer container.
     * @var array
     */
    private $_output = [];

    /**
     * Query parameters container.
     * @var array
     */
    private $_parameter = [];

    /**
     * Upsert (Insert on Duplicate Update) permission status.
     * @var bool
     */
    private $_permit_upsert;

    /**
     * Prepared statement data.
     * @var array
     */
    private $_prepare = [];

    /**
     * Last executed query string.
     * @var string
     */
    private $_query;

    /**
     * Redirect back status/url.
     */
    private $_redirect_back;

    /**
     * Demo mode restriction status.
     * @var bool
     */
    private $_restrict_on_demo;

    /**
     * Query results container.
     * @var array
     */
    private $_results = [];

    /**
     * Searchable status.
     * @var bool
     */
    private $_searchable = true;

    /**
     * Select clause configuration.
     * @var array
     */
    private $_select = [];

    /**
     * Column alias configuration.
     * @var array
     */
    private $_set_alias = [];

    /**
     * HTML attribute configuration.
     * @var array
     */
    private $_set_attribute = [];

    /**
     * Autocomplete configuration.
     * @var array
     */
    private $_set_autocomplete = [];

    /**
     * Breadcrumb configuration.
     * @var array
     */
    private $_set_breadcrumb = [];

    /**
     * Breadcrumb configuration.
     * @var array
     */
    private $_set_button = [];

    /**
     * Default value configuration.
     * @var array
     */
    private $_set_default = [];

    /**
     * Description configuration.
     * @var array
     */
    private $_set_description = [];

    /**
     * Fallback description configuration.
     * @var string
     */
    private $_set_description_fallback;

    /**
     * Field configuration.
     * @var array
     */
    private $_set_field = [];

    /**
     * Page heading configuration.
     * @var array
     */
    private $_set_heading = [];

    /**
     * Icon configuration.
     * @var array
     */
    private $_set_icon = [];

    /**
     * Fallback icon configuration.
     * @var string
     */
    private $_set_icon_fallback;

    /**
     * Flash message configuration.
     * @var array
     */
    private $_set_messages = [];

    /**
     * Method overriding configuration.
     * @var array
     */
    private $_set_method = [];

    /**
     * Option label configuration.
     * @var array
     */
    private $_set_option_label = [];

    /**
     * Output format configuration.
     * @var array
     */
    private $_set_output = [];

    /**
     * Permission configuration.
     * @var bool
     */
    private $_set_permission;

    /**
     * Input placeholder configuration.
     * @var array
     */
    private $_set_placeholder = [];

    /**
     * Primary key configuration used for token generation and validation.
     * @var array
     */
    private $_set_primary = [];

    /**
     * Table relation configuration.
     * @var array
     */
    private $_set_relation = [];

    /**
     * View template configuration.
     * @var array
     */
    private $_set_template = [];

    /**
     * Frontend/Backend theme configuration.
     * @var string
     */
    private $_set_theme = 'default';

    /**
     * Page title configuration.
     * @var array
     */
    private $_set_title = [];

    /**
     * Fallback page title configuration.
     * @var string
     */
    private $_set_title_fallback;

    /**
     * Tooltip configuration.
     * @var array
     */
    private $_set_tooltip = [];

    /**
     * Upload path configuration.
     * @var string
     */
    private $_set_upload_path;

    /**
     * Form validation rules configuration.
     * @var array
     */
    private $_set_validation = [];

    /**
     * Sortable column status.
     * @var bool
     */
    private $_sortable;

    /**
     * Primary table name.
     * @var string
     */
    private $_table;

    /**
     * Total rows count (used for pagination).
     * @var int
     */
    private $_total;

    /**
     * Field translation configuration.
     * @var array
     */
    private $_translate_field = [];

    /**
     * Container for uploaded files.
     * @var array
     */
    private $_uploaded_files = [];

    /**
     * Configuration to disable cloning.
     * @var array
     */
    private $_unset_clone = [];

    /**
     * Configuration to hide columns from view.
     * @var array
     */
    private $_unset_column = [];

    /**
     * Configuration to disable delete operation.
     * @var array
     */
    private $_unset_delete = [];

    /**
     * Configuration to remove fields from form/view.
     * @var array
     */
    private $_unset_field = [];

    /**
     * Configuration to disable specific methods.
     * @var array
     */
    private $_unset_method = [];

    /**
     * Configuration to disable read operation.
     * @var array
     */
    private $_unset_read = [];

    /**
     * Configuration to exclude columns from selection.
     * @var array
     */
    private $_unset_select = [];

    /**
     * Configuration to disable truncate operation.
     * @var array
     */
    private $_unset_truncate = [];

    /**
     * Configuration to disable update operation.
     * @var array
     */
    private $_unset_update = [];

    /**
     * Configuration to disable view operation.
     * @var array
     */
    private $_unset_view = [];

    /**
     * Current view file name.
     * @var string
     */
    private $_view = 'index';

    /**
     * View ordering configuration.
     * @var array
     */
    private $_view_order = [];

    /**
     * Where clause configuration.
     * @var array
     */
    private $_where = [];
}
