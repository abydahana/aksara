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

namespace Aksara\Laboratory\Services;

use Config\Services;
use Aksara\Laboratory\Model;
use Throwable;

/**
 * CRUD Engine Service
 *
 * This class is responsible for orchestrating the CRUD operations,
 * including query building, data persistence, and relational data handling.
 * It acts as the engine that works behind the Core controller.
 *
 * @property \Aksara\Laboratory\Model $model Database model instance
 * @property \CodeIgniter\HTTP\IncomingRequest $request HTTP request instance
 * @property \CodeIgniter\HTTP\Response $response HTTP response instance
 * @property \CodeIgniter\Session\Session $session Session instance
 * @property string $_table Primary table name
 * @property string $_primary Primary key field
 * @property string $_method Current CRUD method (create/read/update/delete)
 * @property array $_select Selected fields
 * @property array $_unsetField Fields to unset in forms
 * @property array $_unsetView Fields to unset in views
 * @property array $_unsetColumn Fields to unset in tables
 * @property bool $_distinct Use DISTINCT in queries
 * @property array $_compiledTable Compiled table data
 */
class Crud
{
    /**
     * Parent controller instance (Core)
     */
    private mixed $controller;
    /**
     * Constructor
     *
     * @param   mixed $controller The calling controller instance
     */
    public function __construct(mixed $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Magic method to access controller properties dynamically.
     */
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        // Return from controller if exists
        return $this->controller->$name;
    }

    /**
     * Internal Query Runner.
     */
    public function runQuery(?string $table = null, bool $recycling = false): mixed
    {
        // Use the table
        $query = $this->model->table($table);

        // Add distinct
        if ($this->controller->_distinct) {
            $query = $this->model->distinct();
        }

        // Check if the request is not recycling the previous properties
        if (! $recycling) {
            // Prepare indexing the columns of table to be selected
            $select = preg_filter('/^/', $table . '.', $this->model->listFields($table));
            $columns = $this->model->fieldData($table);

            if ($columns) {
                foreach ($columns as $key => $val) {
                    if (in_array($this->controller->_method, ['create', 'update'], true) && in_array($val->name, $this->controller->_unsetField, true)) {
                        if (! isset($val->primaryKey) || empty($val->primaryKey)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($this->controller->_method, ['read'], true) && in_array($val->name, $this->controller->_unsetView, true)) {
                        if (! isset($val->primaryKey) || empty($val->primaryKey)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($val->name, $this->controller->_unsetColumn, true)) {
                        if (! isset($val->primaryKey) || empty($val->primaryKey)) {
                            unset($select[$val->name]);
                        }
                    }
                }
            }

            // Merge selection
            if (! in_array($this->controller->_method, ['create', 'update'], true)) {
                $select = ($this->controller->_select ? array_merge($select, $this->controller->_select) : $select);
            }

            // Execute when method is not delete
            if (! in_array($this->controller->_method, ['delete'], true) && is_array($select) && sizeof($select) > 0) {
                // Validate the select column to check if column is exist in table
                $compiledSelect = [];

                foreach ($select as $key => $val) {
                    // Check if field is already selected
                    $val = trim(preg_replace('/\s\s+/', ' ', $val));
                    $alias = (strrpos($val, ' ') !== false ? substr($val, strrpos($val, ' ') + 1) : (strpos($val, '.') !== false ? explode('.', $val) : ['anonymous', $val]));
                    $alias = (is_array($alias) && isset($alias[1]) ? $alias[1] : $alias);

                    // Check if selected column is use alias
                    if (strpos($val, '.*') !== false && strstr($val, '.*', true) == $table) {
                        continue;
                    } else {
                        // Individual table
                        list($backupTable, $field) = array_pad(explode('.', $val), 2, null);

                        if (! $field) {
                            $field = $backupTable;
                            $backupTable = $table;
                        }

                        // Get the name alias
                        $field = trim(($field && stripos($field, ' AS ') !== false ? substr($field, strripos($field, ' AS ') + 4) : $field));

                        if ($field && stripos($field, ' ') !== false) {
                            $field = substr($field, 0, strrpos($field, ' '));
                        }

                        if ($backupTable != $table && $field && $this->model->fieldExists($field, $backupTable)) {
                            // Format column of select
                            $val = $backupTable . '.' . $field . ' AS ' . $field;
                        }
                    }

                    // Compile the selected field
                    $compiledSelect[$alias] = $val;
                }

                // Check if select compiled
                if ($compiledSelect) {
                    // Ready for unique selection
                    foreach ($this->controller->_prepare as $key => $val) {
                        if ('select' == $val['function']) {
                            // Unset previous prepared select
                            unset($this->controller->_prepare[$key]);
                        }
                    }

                    // Push compiled select to prepared query builder
                    $this->controller->_prepare[] = [
                        'function' => 'select',
                        'arguments' => [array_values($compiledSelect)]
                    ];
                }

                // Generate join query passed from set_relation
                if (is_array($this->controller->_join) && sizeof($this->controller->_join) > 0) {
                    foreach ($this->controller->_join as $table => $params) {
                        // Push join to prepared query builder
                        $this->controller->_prepare[] = [
                            'function' => 'join',
                            'arguments' => [$table, str_replace('__PRIMARY_TABLE__', $this->controller->_table, $params['condition']), $params['type'], $params['escape']]
                        ];
                    }
                }
            }
        }

        // Format compiled select
        if ($this->controller->_compiledSelect) {
            foreach ($this->controller->_compiledSelect as $key => $val) {
                // Check if column should be unset
                if (in_array($val, $this->controller->_unsetSelect, true)) {
                    // Unset selected compiled select
                    unset($this->controller->_compiledSelect[$key]);
                }
            }
        }

        // Run generated query builder
        foreach ($this->controller->_prepare as $key => $val) {
            $function = $val['function'];
            $arguments = $val['arguments'];

            if ('select' == $function) {
                // Slice unnecessary select
                if (! is_array($arguments[0])) {
                    // Explode comma sparated string to array
                    $arguments[0] = array_map('trim', explode(',', $arguments[0]));
                }

                // Prevent duplicate entries
                $arguments[0] = array_unique($arguments[0]);

                // Looping the argument
                foreach ($arguments[0] as $_key => $_val) {
                    $column = $_val;
                    $alias = null;

                    // Check whether generated selected columns need to unset
                    if (in_array($column, $this->controller->_unsetSelect, true)) {
                        // Unset unselected columns
                        unset($arguments[0][$_key]);

                        continue;
                    }

                    // Find bracket wrapper or continue on void
                    if (strpos($_val, '(') === false && strpos($_val, ')') === false) {
                        // Now find dotted table and column pairs
                        if (stripos($column, '.') !== false) {
                            // Extract column
                            $column = substr($column, stripos($column, '.') + 1);
                        }

                        // Now find if column is aliased
                        if (stripos(trim($column), ' AS ') !== false) {
                            // Find alias
                            $alias = substr($column, stripos($column, ' AS ') + 4);

                            // Assign to column
                            $column = substr($column, 0, strpos($_val, ' AS '));
                        }

                        // Store selection keys
                        $compiledSelectKey1 = array_search($column, $this->controller->_compiledSelect);
                        $compiledSelectKey2 = array_search($alias, $this->controller->_compiledSelect);

                        // Unset matched compiled select
                        unset($this->controller->_compiledSelect[$compiledSelectKey1]);
                        unset($this->controller->_compiledSelect[$compiledSelectKey2]);

                        // Extract source table
                        $sourceTable = substr($_val . '.', 0, strpos($_val, '.'));

                        // Check whether table or columns has compiled
                        if (! in_array($sourceTable, $this->controller->_compiledTable, true) && ! $alias) {
                            // Field doesn't exists on compiled table
                            unset($arguments[0][$_key]);
                        }
                    }
                }

                // Make the selection column unique
                $arguments[0] = array_unique(array_merge($this->controller->_compiledSelect, $arguments[0]));
            } elseif ('where' == $function) {
                // Extract source table from selection
                $sourceTable = (isset($arguments[0]) ? $arguments[0] : '');
                $sourceTable = substr($sourceTable . '.', 0, strpos($sourceTable, '.'));

                if ($sourceTable && ! in_array($sourceTable, $this->controller->_compiledTable, true)) {
                    // Source table not in compilation
                    continue;
                }

                if (! preg_match('/[.<=>()]/', $arguments[0])) {
                    // Add table prefix to field
                    $arguments[0] = $this->controller->_table . '.' . $arguments[0];
                }
            } elseif ('select_subquery' == $function) {
                // Free query builder
                $this->model->resetQuery();
            } elseif ('orderBy' == $function && in_array($this->controller->_method, ['create', 'read', 'update', 'delete'], true)) {
                // Prevent order on CRUD
                continue;
            }

            if (is_array($arguments) && sizeof($arguments) == 7) {
                // Run model method with 7 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
            } elseif (is_array($arguments) && sizeof($arguments) == 6) {
                // Run model method with 6 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
            } elseif (is_array($arguments) && sizeof($arguments) == 5) {
                // Run model method with 5 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            } elseif (is_array($arguments) && sizeof($arguments) == 4) {
                // Run model method with 4 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            } elseif (is_array($arguments) && sizeof($arguments) == 3) {
                // Run model method with 3 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2]);
            } elseif (is_array($arguments) && sizeof($arguments) == 2) {
                // Run model method with 2 parameters
                $query = $this->model->$function($arguments[0], $arguments[1]);
            } else {
                // Run model method with single parameter
                $query = $this->model->$function((isset($arguments[0]) ? $arguments[0] : $arguments));
            }
        }

        return $query;
    }

    /**
     * Fetches the data by running the prepared query builder parameters.
     */
    public function fetch(?string $table = null, ?bool $row = false): array
    {
        // --- 1. Debugger ---
        if ($this->controller->_debugging) {
            // Run query with limit/offset for debug output
            $queryBuilder = $this->runQuery($table);

            if (null !== $this->controller->_limit) {
                $queryBuilder->limit($this->controller->_limit, $this->controller->_offset ?? 0);
            }

            if ($row) {
                $query = $queryBuilder->row();
            } else {
                $query = $queryBuilder->result();
            }

            if ('query' == $this->controller->_debugging) {
                exit(nl2br($this->model->lastQuery()));
            } else {
                if (ENVIRONMENT === 'production') {
                    exit('<pre>' . print_r($query, true) . '</pre>');
                }
                dd($query);
            }
        }

        // --- 2. Execute Queries ---
        $resultsBuilder = $this->runQuery($table);

        if (null !== $this->controller->_limit) {
            $resultsBuilder->limit($this->controller->_limit, $this->controller->_offset ?? 0);
        }

        if ($row) {
            $results = $resultsBuilder->row();
            $total = ($results ? 1 : 0);
        } else {
            $results = $resultsBuilder->result();
            $total = $this->runQuery($table, true)->countAllResults();
        }

        // --- 3. Reset and Return ---
        $this->controller->_prepare = [];

        return [
            'results' => $results,
            'total' => $total
        ];
    }

    /**
     * Retrieves related table data for a relational field.
     */
    public function getRelation(array $params = [], int|string|null $selected = 0, bool $ajax = false): array|string
    {
        // Use default value if nothing is selected and a default is defined.
        $fieldNameForDefault = is_array($params['primary_key']) ? end($params['primary_key']) : ($params['primary_key'] ?? null);
        if (! $selected && ($this->controller->_defaultValue[$fieldNameForDefault] ?? null)) {
            $selected = $this->controller->_defaultValue[$fieldNameForDefault];
        }

        $compiledSelect = [];
        $like = [];
        $primaryKey = is_array($params['primary_key']) ? end($params['primary_key']) : ($params['primary_key'] ?? null);

        // --- 1. SELECT and LIKE Clause Construction ---
        foreach ($params['select'] as $key => $val) {
            $parts = explode('.', $val);
            $column = $parts[1] ?? $val;
            $table = $parts[0] ?? null;

            // Handle column aliasing to prevent ambiguity if column names clash.
            if (in_array($column, $compiledSelect, true) && $table != $this->controller->_table) {
                $val .= ' AS ' . $column . '_' . $table;
            }

            $this->model->select($val);
            $compiledSelect[] = $column;

            // Build LIKE clause for search payload (used in AJAX).
            if ($search = $this->request->getPost('search')) {
                $likeKey = (stripos($val, ' AS ') !== false) ? substr($val, 0, stripos($val, ' AS ')) : $val;
                $like[$likeKey] = $search;
            }
        }

        // Apply LIKE clauses if present and not retrieving a single selected item.
        if ($like && ! $selected) {
            $this->model->groupStart();
            $num = 0;
            foreach ($like as $key => $val) {
                $this->model->{(($num) ? 'orLike' : 'like')}($key, $val, 'both', true, true);
                $num++;
            }
            $this->model->groupEnd();
        }

        // --- 2. JOIN Clause Construction ---
        if ($params['join']) {
            foreach ($params['join'] as $val) {
                if (! isset($val[0], $val[1])) {
                    continue;
                }
                $this->model->join($val[0], $val[1], $val[2] ?? '');
            }
        }

        // --- 3. WHERE Clause Modification for Selected Item ---
        if ($selected) {
            $relationTable = (strpos($params['relation_table'], ' ') !== false) ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table'];
            $relationKey = $relationTable . '.' . $params['relation_key'];
            $params['where'][$relationKey] = $selected;
            $params['limit'] = 1;
        }

        // --- 4. Apply Custom WHERE Clauses ---
        if ($params['where']) {
            foreach ($params['where'] as $key => $val) {
                if (is_numeric(strpos($key, ' IN')) || is_numeric(strpos($key, ' NOT IN'))) {
                    $this->model->where($key, $val, false);
                } elseif (is_numeric(strpos($val, ' IN')) || is_numeric(strpos($val, ' NOT IN'))) {
                    $this->model->where($val, null, false);
                } else {
                    $this->model->where($key, $val);
                }
            }
        }

        if (! in_array($this->controller->_method, ['create', 'update'], true) && $selected) {
            $relationTableName = (strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']);

            if (is_array($params['relation_key'])) {
                $selectedParts = explode('.', $selected);
                foreach ($params['relation_key'] as $k => $relKey) {
                    if ($selectedParts[$k] ?? null) {
                        $this->model->where($relationTableName . '.' . $relKey, $selectedParts[$k]);
                    }
                }
            } else {
                $this->model->where($relationTableName . '.' . $params['relation_key'], $selected);
            }
        }

        // --- 5. Apply ORDER BY and GROUP BY ---
        if ($params['orderBy'] && ! $selected) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $val) {
                    $this->model->orderBy($key, $val);
                }
            } else {
                $this->model->orderBy($params['orderBy']);
            }
        }

        if ($params['join'] && $params['groupBy'] && ! $selected) {
            $this->model->groupBy($params['groupBy']);
        }

        // --- 6. Initialize Output Array ---
        $output = [];
        if (! $selected) {
            if ($ajax) {
                if ($this->request->getPost('page') <= 1) {
                    $output[] = ['id' => 0, 'text' => phrase('None')];
                }
            } else {
                $output[] = ['value' => 0, 'label' => phrase('None'), 'selected' => false];
            }
        }

        // --- 7. Run Query and Format Results ---
        $query = $this->model->get($params['relation_table'], $params['limit'], $params['offset'])->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $label = $params['output'];
                $attributes = $this->controller->_setAttribute[$primaryKey] ?? '';
                $optionLabel = $this->controller->_setOptionLabel[$primaryKey] ?? '';

                foreach ($params['select'] as $magic => $replace) {
                    $replacement = $replace;
                    if (strpos($replace, ' AS ') !== false) {
                        $replacement = substr($replace, strripos($replace, ' AS ') + 4);
                    } elseif (strpos($replace, '.') !== false) {
                        $replacement = substr($replace, strripos($replace, '.') + 1);
                    }

                    if (isset($val->$replacement)) {
                        if (isset($this->controller->_setField[$replacement]['sprintf'])) {
                            $val->$replacement = sprintf('%02d', $val->$replacement);
                        }

                        $pattern = "/\{\{(\s+)?($replace)(\s+)?\}\}/";
                        $label = preg_replace($pattern, $val->$replacement, $label);
                        $attributes = preg_replace($pattern, $val->$replacement, $attributes);
                        $optionLabel = preg_replace($pattern, $val->$replacement, $optionLabel);
                    }
                }

                if (in_array($this->controller->_method, ['create', 'update'], true)) {
                    $value = $val->$primaryKey ?? $val->$params['relation_key'] ?? 0;
                    $isSelected = ($value == $selected);

                    if (is_array($params['primary_key'])) {
                        $value = implode('.', array_map(fn ($k) => $val->$k ?? 0, $params['primary_key']));
                        $isSelected = ($value == $selected);
                    }

                    if ($ajax) {
                        $output[] = ['id' => $value, 'text' => ($params['translate'] ? phrase($label) : $label)];
                    } else {
                        $output[] = ['value' => $value, 'label' => ($params['translate'] ? phrase($label) : $label), 'selected' => $isSelected];
                    }
                } else {
                    $output = ($params['translate'] ? phrase($label) : $label);
                    return $output;
                }
            }
        }

        // --- 8. Final Output Return ---
        if ($ajax) {
            return make_json([
                'results' => $output,
                'pagination' => ['more' => ($output && count($output) >= $params['limit'])]
            ]);
        }

        return $output;
    }

    /**
     * Executes the drag-and-drop sorting of table rows.
     */
    public function sortTable(array $orderedId = []): string
    {
        if (! $this->controller->_sortable || ! is_array($orderedId)) {
            return make_json([
                'status' => 400,
                'message' => phrase('The order format is invalid.')
            ]);
        }

        $primaryKeyField = $this->controller->_sortable['primary_key'];
        $orderKeyField = $this->controller->_sortable['order_key'];

        $query = $this->model->select($primaryKeyField)
            ->select($orderKeyField)
            ->whereIn($primaryKeyField, $orderedId)
            ->orderBy($orderKeyField, 'ASC')
            ->getWhere($this->controller->_table, [])
            ->resultArray();

        $newOrder = [];
        foreach ($query as $val) {
            $newOrder[] = $val[$orderKeyField];
        }

        foreach ($orderedId as $key => $val) {
            $this->model->update(
                $this->controller->_table,
                [
                    $orderKeyField => $newOrder[$key]
                ],
                [
                    $primaryKeyField => $val
                ]
            );
        }

        return make_json([
            'status' => 200,
            'message' => phrase('The data was sorted successfully.')
        ]);
    }

    /**
     * Unlinks uploaded files and their associated thumbnails/icons.
     */
    public function unlinkFiles(?array $files = [], ?string $fieldName = null, array $fieldList = []): void
    {
        foreach ($files ?? [] as $field => $src) {
            if (is_string($src) && is_json($src)) {
                $src = json_decode($src, true);
            }

            if (is_array($src)) {
                $newFieldName = $fieldName ?? ($field . '_label');
                $fieldList[$newFieldName] = array_merge($fieldList[$newFieldName] ?? [], $src);
                $this->unlinkFiles($src, $newFieldName, $fieldList);
                continue;
            }

            $inputName = urldecode(http_build_query($fieldList));
            $inputName = substr($inputName, 0, strpos($inputName, '='));
            $fileUploadedEmpty = (! is_array($field) && isset($_FILES[$field]['tmp_name']) && empty($_FILES[$field]['tmp_name']));

            if ('placeholder.png' == $src || $this->request->getPost($inputName) || $fileUploadedEmpty) {
                continue;
            }

            $safeSrc = basename($src);
            $safeField = basename((string) $field);
            $filesToCheck = [$safeSrc, $safeField];
            $subdirectories = ['', 'thumbs/', 'icons/'];
            $baseDir = UPLOAD_PATH . '/' . $this->controller->_setUploadPath . '/';

            foreach ($subdirectories as $subdir) {
                foreach ($filesToCheck as $filename) {
                    $path = $baseDir . $subdir . $filename;
                    if ($filename && is_file($path)) {
                        try {
                            unlink($path);
                        } catch (Throwable $e) {
                        }
                    }
                }
            }
        }
    }

    /**
     * CRUD Action: Insert Data.
     */
    public function insertData(?string $table = null, array $data = []): object|null
    {
        if ($this->controller->apiClient && ! in_array($this->request->getMethod(), ['POST'], true)) {
            $this->unlinkFiles(get_userdata('_uploaded_files'));
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->controller->_redirectBack);
        }

        if ($table && $this->model->tableExists($table)) {
            if (method_exists($this->controller, 'beforeInsert')) {
                $this->controller->beforeInsert();
            }

            if ($this->model->insert($table, $data)) {
                $autoIncrement = true;
                $primary = 0;

                if ('Postgre' == $this->controller->_dbDriver) {
                    $autoIncrement = false;
                    $fieldData = $this->model->fieldData($table);
                    foreach ($fieldData as $val) {
                        if (isset($this->controller->_setDefault[$val->name])) {
                            $primary = $this->controller->_setDefault[$val->name];
                        }
                        if (($val->primaryKey ?? false) || (isset($val->default) && $val->default && stripos($val->default, 'nextval(') !== false)) {
                            $autoIncrement = true;
                        }
                        if ($primary && $autoIncrement) {
                            break;
                        }
                    }
                }

                $this->controller->_insertId = $autoIncrement ? $this->model->insertId() : 0;

                unset_userdata('_uploaded_files');
                unset_userdata(sha1(current_page() . get_userdata('session_generated') . ENCRYPTION_KEY));

                if (method_exists($this->controller, 'afterInsert')) {
                    $this->controller->afterInsert();
                }

                set_userdata('token_timestamp', time());
                unset_userdata(sha1(uri_string()));

                return throw_exception(($this->controller->apiClient ? 200 : 301), phrase('The data was successfully submitted.'), $this->controller->_redirectBack);
            } else {
                $this->unlinkFiles(get_userdata('_uploaded_files'));
                $error = $this->model->error();
                $errorMessage = $error['message'] ?? phrase('Unable to submit your data.');

                if (get_userdata('group_id') == 1 && ENVIRONMENT != 'production') {
                    $finalMessage = $errorMessage;
                } else {
                    $finalMessage = phrase('Unable to submit your data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (INSERT)</b>';
                }

                return throw_exception(500, $finalMessage, $this->controller->_redirectBack);
            }
        } else {
            $this->unlinkFiles(get_userdata('_uploaded_files'));
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->controller->_redirectBack);
        }
    }

    /**
     * CRUD Action: Update Data.
     */
    public function updateData(?string $table = null, array $data = [], array $where = []): object|bool
    {
        if ($this->controller->apiClient && ! in_array($this->request->getMethod(), ['POST'], true)) {
            $this->unlinkFiles(get_userdata('_uploaded_files'));
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->controller->_redirectBack);
        }

        if ($table && $this->model->tableExists($table)) {
            if (! $where) {
                $fieldExists = array_flip($this->model->listFields($table));
                $where = array_intersect_key($this->request->getGet(), $fieldExists);

                if (! $where) {
                    $this->unlinkFiles(get_userdata('_uploaded_files'));
                    return throw_exception(404, phrase('The data you would to update is not found.'), $this->controller->_redirectBack);
                }

                foreach ($where as $keyBackup => $val) {
                    $key = (stripos($keyBackup, '.') !== false) ? substr($keyBackup, stripos($keyBackup, '.') + 1) : $keyBackup;
                    if (! $this->model->fieldExists($key, $table)) {
                        unset($where[$keyBackup]);
                    }
                }
            }

            $query = $this->model->getWhere($table, $where, 1)->row();

            if ($query) {
                if (method_exists($this->controller, 'beforeUpdate')) {
                    $this->controller->beforeUpdate();
                }

                $oldFiles = [];
                foreach ($query as $field => $value) {
                    if (isset($this->controller->_setField[$field]['field_type']) && array_intersect($this->controller->_setField[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $oldFiles[$field] = $value;
                    }
                }

                if ($this->model->update($table, $data, $where)) {
                    unset_userdata('_uploaded_files');
                    unset_userdata(sha1(current_page() . get_userdata('session_generated') . ENCRYPTION_KEY));
                    $this->unlinkFiles($oldFiles);

                    if (method_exists($this->controller, 'afterUpdate')) {
                        $this->controller->afterUpdate();
                    }

                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));

                    return throw_exception(($this->controller->apiClient ? 200 : 301), phrase('The data was successfully updated.'), $this->controller->_redirectBack);
                } else {
                    $this->unlinkFiles(get_userdata('_uploaded_files'));
                    $error = $this->model->error();
                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->controller->_redirectBack);
                    }
                    return throw_exception(500, phrase('Unable to update the data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (UPDATE)</b>', $this->controller->_redirectBack);
                }
            } elseif ($this->controller->_permitUpsert) {
                return $this->insertData($table, $data);
            } else {
                $this->unlinkFiles(get_userdata('_uploaded_files'));
                return throw_exception(404, phrase('The data you would to update is not found.'), $this->controller->_redirectBack);
            }
        } else {
            $this->unlinkFiles(get_userdata('_uploaded_files'));
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->controller->_redirectBack);
        }

        return false;
    }

    /**
     * CRUD Action: Delete Data.
     */
    public function deleteData(?string $table = null, array $where = [], int $limit = 1): object|null
    {
        if ($this->controller->apiClient && ! in_array($this->request->getMethod(), ['DELETE'], true)) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->controller->_redirectBack);
        }

        if ($this->controller->_restrictOnDemo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->controller->_redirectBack);
        }

        if (isset($this->controller->_setMessages['delete']) && ($this->controller->_setMessages['delete']['return'] ?? false)) {
            return throw_exception($this->controller->_setMessages['delete']['code'], $this->controller->_setMessages['delete']['messages'], $this->controller->_redirectBack);
        }

        if ($table && $this->model->tableExists($table)) {
            if (! $where) {
                $fieldExists = array_flip($this->model->listFields($table));
                $where = array_intersect_key($this->request->getGet(), $fieldExists);
                if (! $where) {
                    return throw_exception(404, phrase('The data you would to delete is not found.'), $this->controller->_redirectBack);
                }
                foreach ($where as $keyBackup => $val) {
                    $key = (stripos($keyBackup, '.') !== false) ? substr($keyBackup, stripos($keyBackup, '.') + 1) : $keyBackup;
                    if (! $this->model->fieldExists($key, $table)) {
                        unset($where[$keyBackup]);
                    }
                }
            }

            $query = $this->model->getWhere($table, $where, 1)->row();

            if ($query) {
                if (method_exists($this->controller, 'beforeDelete')) {
                    $this->controller->beforeDelete();
                }

                $oldFiles = [];
                foreach ($query as $field => $value) {
                    if (isset($this->controller->_setField[$field]['field_type']) && array_intersect($this->controller->_setField[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $oldFiles[$field] = $value;
                    }
                }

                if ($this->model->delete($table, $where, $limit)) {
                    $this->unlinkFiles($oldFiles);
                    if (method_exists($this->controller, 'afterDelete')) {
                        $this->controller->afterDelete();
                    }
                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));
                    return throw_exception(($this->controller->apiClient ? 200 : 301), phrase('The data was successfully deleted.'), $this->controller->_redirectBack);
                } else {
                    $error = $this->model->error();
                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->controller->_redirectBack);
                    }
                    return throw_exception(500, phrase('Unable to delete the data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (DELETE)</b>', $this->controller->_redirectBack);
                }
            }
        }
        return null;
    }

    /**
     * CRUD Action: Delete Batch.
     */
    public function deleteBatch(?string $table = null): object|null
    {
        if ($this->controller->apiClient && ! in_array($this->request->getMethod(), ['DELETE'], true)) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->controller->_redirectBack);
        }

        if ($this->controller->_restrictOnDemo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->controller->_redirectBack);
        }

        if ($table && $this->model->tableExists($table)) {
            $batch = $this->request->getPost('batch');
            if ($batch) {
                $count = 0;
                foreach ($batch as $key => $val) {
                    // This is a bit simplified compared to Core.php as it usually handles complex batch.
                    // But let's follow the standard delete logic for each item if possible,
                    // or use a simplified batch delete.
                    $this->deleteData($table, $val);
                    $count++;
                }
                return throw_exception(($this->controller->apiClient ? 200 : 301), phrase($count . ' data was successfully deleted.'), $this->controller->_redirectBack);
            }
        }
        return throw_exception(404, phrase('The data you would to delete is not found.'), $this->controller->_redirectBack);
    }
}
