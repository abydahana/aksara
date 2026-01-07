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
    private $_addClass = [];

    /**
     * Container for custom buttons.
     * @var array
     */
    private $_addButton = [];

    /**
     * Container for custom dropdown menus.
     * @var array
     */
    private $_addDropdown = [];

    /**
     * Container for custom filters.
     * @var array
     */
    private $_addFilter = [];

    /**
     * Container for custom toolbar items.
     * @var array
     */
    private $_addToolbar = [];

    /**
     * Callback to execute after a delete operation.
     * @var callable|null
     */
    private $_afterDelete;

    /**
     * Callback to execute after an insert operation.
     * @var callable|null
     */
    private $_afterInsert;

    /**
     * Callback to execute after an update operation.
     * @var callable|null
     */
    private $_afterUpdate;

    /**
     * Callback to execute before a delete operation.
     * @var callable|null
     */
    private $_beforeDelete;

    /**
     * Callback to execute before an insert operation.
     * @var callable|null
     */
    private $_beforeInsert;

    /**
     * Callback to execute before an update operation.
     * @var callable|null
     */
    private $_beforeUpdate;

    /**
     * Cloning mode status.
     * @var bool
     */
    private $_cloning;

    /**
     * Column ordering configuration for tables.
     * @var array
     */
    private $_columnOrder = [];

    /**
     * Column size configuration.
     * @var array
     */
    private $_columnSize = [];

    /**
     * Store the compiled SELECT statement.
     * @var array
     */
    private $_compiledSelect = [];

    /**
     * Store the compiled table name.
     * @var array
     */
    private $_compiledTable = [];

    /**
     * Container for retrieved data.
     * @var array
     */
    private $_data = [];

    /**
     * Database driver name.
     * @var string
     */
    private $_dbDriver;

    /**
     * Debugging mode status.
     * @var bool
     */
    private $_debugging;

    /**
     * Default value configuration for form fields.
     * @var array
     */
    private $_defaultValue = [];

    /**
     * Distinct query status.
     * @var bool
     */
    private $_distinct;

    /**
     * Container for extra dropdown options.
     * @var array
     */
    private $_extraDropdown = [];

    /**
     * Container for extra options.
     * @var array
     */
    private $_extraOption = [];

    /**
     * Container for extra submit buttons.
     * @var array
     */
    private $_extraSubmit = [];

    /**
     * Container for extra toolbar items.
     * @var array
     */
    private $_extraToolbar = [];

    /**
     * Field suffix configuration (append).
     * @var array
     */
    private $_fieldAppend = [];

    /**
     * Field ordering configuration for forms.
     * @var array
     */
    private $_fieldOrder = [];

    /**
     * Field positioning configuration.
     * @var array
     */
    private $_fieldPosition = [];

    /**
     * Field prefix configuration (prepend).
     * @var array
     */
    private $_fieldPrepend = [];

    /**
     * Input field size configuration.
     * @var array
     */
    private $_fieldSize = [];

    /**
     * Callback for form processing.
     * @var callable|null
     */
    private $_formCallback;

    /**
     * Grid view mode status.
     * @var bool
     */
    private $_gridView;

    /**
     * Field grouping configuration.
     * @var array
     */
    private $_groupField = [];

    /**
     * Flag to skip URL signature validation (HMAC).
     * Use this for public search pages or reports with dynamic GET parameters.
     * @var array
     */
    private $_ignoreQueryString = [];

    /**
     * Last inserted ID.
     * @var int|string
     */
    private $_insertId;

    /**
     * Container for referenced items.
     * @var array
     */
    private $_itemReference = [];

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
    private $_limitBackup = 25;

    /**
     * Content merge configuration.
     * @var array
     */
    private $_mergeContent = [];

    /**
     * Field merge configuration.
     * @var array
     */
    private $_mergeField = [];

    /**
     * Label merge configuration.
     * @var array
     */
    private $_mergeLabel = [];

    /**
     * Request method (GET, POST, etc.) or Controller method.
     * @var string
     */
    private $_method;

    /**
     * Modal dialog size configuration.
     * @var string
     */
    private $_modalSize;

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
    private $_offsetCalled = false;

    /**
     * Container for old files (used during update).
     */
    private $_oldFiles;

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
    private $_permitUpsert;

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
    private $_redirectBack;

    /**
     * Demo mode restriction status.
     * @var bool
     */
    private $_restrictOnDemo;

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
    private $_setAlias = [];

    /**
     * HTML attribute configuration.
     * @var array
     */
    private $_setAttribute = [];

    /**
     * Autocomplete configuration.
     * @var array
     */
    private $_setAutocomplete = [];

    /**
     * Breadcrumb configuration.
     * @var array
     */
    private $_setBreadcrumb = [];

    /**
     * Button configuration.
     * @var array
     */
    private $_setButton = [];

    /**
     * Default value configuration.
     * @var array
     */
    private $_setDefault = [];

    /**
     * Description configuration.
     * @var string
     */
    private $_setDescription;

    /**
     * Description fallback configuration.
     * @var array
     */
    private $_setDescriptionFallback = [];

    /**
     * Field configuration.
     * @var array
     */
    private $_setField = [];

    /**
     * Page heading configuration.
     * @var array
     */
    private $_setHeading = [];

    /**
     * Icon configuration.
     * @var string
     */
    private $_setIcon;

    /**
     * Icon fallback configuration.
     * @var array
     */
    private $_setIconFallback = [];

    /**
     * Flash message configuration.
     * @var array
     */
    private $_setMessages = [];

    /**
     * Method overriding configuration.
     * @var array
     */
    private $_setMethod = [];

    /**
     * Option label configuration.
     * @var array
     */
    private $_setOptionLabel = [];

    /**
     * Output format configuration.
     * @var array
     */
    private $_setOutput = [];

    /**
     * Permission configuration.
     * @var bool
     */
    private $_setPermission;

    /**
     * Input placeholder configuration.
     * @var array
     */
    private $_setPlaceholder = [];

    /**
     * Primary key configuration used for token generation and validation.
     * @var array
     */
    private $_setPrimary = [];

    /**
     * Table relation configuration.
     * @var array
     */
    private $_setRelation = [];

    /**
     * View template configuration.
     * @var string
     */
    private $_setTemplate;

    /**
     * Theme configuration.
     * @var string
     */
    private $_setTheme;

    /**
     * Page title configuration.
     * @var string
     */
    private $_setTitle;

    /**
     * Upload path configuration.
     * @var string
     */
    private $_setUploadPath;

    /**
     * Form validation rules configuration.
     * @var array
     */
    private $_setValidation = [];

    /**
     * Sortable column status.
     * @var bool
     */
    private $_sortable;

    /**
     * Tooltip configuration.
     * @var array
     */
    private $_setTooltip = [];

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
    private $_translateField = [];

    /**
     * Container for uploaded files.
     * @var array
     */
    private $_uploadedFiles = [];

    /**
     * Configuration to disable cloning.
     * @var array
     */
    private $_unsetClone = [];

    /**
     * Configuration to hide columns from view.
     * @var array
     */
    private $_unsetColumn = [];

    /**
     * Configuration to disable delete operation.
     * @var array
     */
    private $_unsetDelete = [];

    /**
     * Configuration to remove fields from form/view.
     * @var array
     */
    private $_unsetField = [];

    /**
     * Configuration to disable specific methods.
     * @var array
     */
    private $_unsetMethod = [];

    /**
     * Configuration to disable read operation.
     * @var array
     */
    private $_unsetRead = [];

    /**
     * Configuration to exclude columns from selection.
     * @var array
     */
    private $_unsetSelect = [];

    /**
     * Configuration to disable truncate operation.
     * @var array
     */
    private $_unsetTruncate = [];

    /**
     * Configuration to disable update operation.
     * @var array
     */
    private $_unsetUpdate = [];

    /**
     * Configuration to disable view operation.
     * @var array
     */
    private $_unsetView = [];

    /**
     * Current view file name.
     * @var string
     */
    private $_view = 'index';

    /**
     * View ordering configuration.
     * @var array
     */
    private $_viewOrder = [];

    /**
     * Where clause configuration.
     * @var array
     */
    private $_where = [];
}
