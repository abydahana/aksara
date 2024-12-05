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

class Model
{
    private $_called;

    private $_finished;

    private $_from;

    private $_get;

    private $_is_query;

    private $_limit;

    private $_offset;

    private $_ordered;

    private $_prepare;

    private $_selection;

    private $_set = [];

    private $_table;

    private $_builder;

    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Use third party database on the fly
     *
     * @param   mixed|null $driver
     */
    public function database_config($driver = null, ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null)
    {
        if (! $driver) {
            // No config provided, use default connection instead
            $this->db = \Config\Database::connect();

            // Unset environment variables
            unset($_ENV['DBDriver'], $_ENV['hostname'], $_ENV['port'], $_ENV['username'], $_ENV['password'], $_ENV['database'], $_ENV['DBDebug']);

            return false;
        }

        // Define config
        $config = [];

        /**
         * Check if "default" or given connection number (from app__connections)
         * is selected
         */
        if ((is_numeric($driver) || 'default' == $driver) && ! $this->_called) {
            try {
                $builder = $this->db->table('app__connections');

                if ('default' == $driver) {
                    $builder->where('year', (get_userdata('year') ? get_userdata('year') : date('Y')));
                } else {
                    $builder->where('id', $driver);
                }

                $parameter = $builder->getWhere(
                    [
                        'status' => 1
                    ],
                    1
                )
                ->getRow();

                $config = [
                    'DBDriver' => $parameter->database_driver,
                    'hostname' => $parameter->hostname,
                    'port' => $port,
                    'username' => service('encrypter')->decrypt(base64_decode($parameter->username)),
                    'password' => service('encrypter')->decrypt(base64_decode($parameter->password)),
                    'database' => $parameter->database_name,
                    'DBDebug' => (ENVIRONMENT !== 'production')
                ];

                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($config);

                // Try to initialize the connection
                $this->db->initialize();

                $this->_called = true;

                // Store environment variables
                $_ENV['DBDriver'] = $config['DBDriver'];
                $_ENV['hostname'] = $config['hostname'];
                $_ENV['port'] = $config['port'];
                $_ENV['username'] = $config['username'];
                $_ENV['password'] = $config['password'];
                $_ENV['database'] = $config['database'];
                $_ENV['DBDebug'] = (ENVIRONMENT !== 'production');
            } catch (\Throwable $e) {
                // Decrypt error
                return throw_exception(403, $e->getMessage());
            }
        } elseif (isset($driver['DBDriver']) && isset($driver['hostname']) && isset($driver['username']) && isset($driver['database'])) {
            try {
                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($driver);

                // Try to initialize the connection
                $this->db->initialize();
            } catch (\Throwable $e) {
                return throw_exception(403, $e->getMessage());
            }
        } elseif ($driver && $hostname && $username && $database) {
            $config = [
                'DBDriver' => $driver,
                'hostname' => $hostname,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'database' => $database,
                'DBDebug' => (ENVIRONMENT !== 'production')
            ];

            try {
                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($config);

                // Try to initialize the connection
                $this->db->initialize();

                // Store environment variables
                $_ENV['DBDriver'] = $config['DBDriver'];
                $_ENV['hostname'] = $config['hostname'];
                $_ENV['port'] = $config['port'];
                $_ENV['username'] = $config['username'];
                $_ENV['password'] = $config['password'];
                $_ENV['database'] = $config['database'];
                $_ENV['DBDebug'] = (ENVIRONMENT !== 'production');
            } catch (\Throwable $e) {
                return throw_exception(403, $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Get the database driver
     */
    public function db_driver()
    {
        return $this->db->DBDriver;
    }

    /**
     * Disable foreign key check for truncating the table
     */
    public function disable_foreign_key()
    {
        $this->db->disableForeignKeyChecks();
    }

    /**
     * Enable foreign key check for truncating the table
     */
    public function enable_foreign_key()
    {
        $this->db->enableForeignKeyChecks();
    }

    /**
     * List available tables on current active database
     */
    public function list_tables()
    {
        return $this->db->listTables();
    }

    /**
     * Check the existing of table on current active database
     *
     * @param   mixed|null $table
     */
    public function table_exists($table = null)
    {
        if ($table && $this->db->tableExists($table)) {
            return true;
        }

        return false;
    }

    /**
     * Check the field existence of selected table
     *
     * @param   mixed|null $field
     * @param   mixed|null $table
     */
    public function field_exists($field = null, $table = null)
    {
        if (strpos(trim($table), '(') !== false || strpos(strtolower(trim($table)), 'select ') !== false) {
            return false;
        }

        if (strpos(trim($table), ' ') !== false) {
            $table = str_ireplace(' AS ', ' ', $table);
            $destructure = explode(' ', $table);
            $table = $destructure[0];

            $this->_table_alias[$destructure[1]] = $table;
        }

        if ($table && $field && $this->db->tableExists($table) && $this->db->fieldExists($field, $table)) {
            return true;
        }

        return false;
    }

    /**
     * List the field of selected table
     *
     * @param   mixed|null $table
     */
    public function list_fields($table = null)
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getFieldNames($table);
        }

        return false;
    }

    /**
     * Get the table metadata and field info of selected table
     *
     * @param   mixed|null $table
     */
    public function field_data($table = null)
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getFieldData($table);
        }

        return false;
    }

    /**
     * Get the table index data of selected table
     *
     * @param   mixed|null $table
     */
    public function index_data($table = null)
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getIndexData($table);
        }

        return false;
    }

    /**
     * Get the  table foreign data of selected table
     *
     * @param   mixed|null $table
     */
    public function foreign_data($table = null)
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getForeignKeyData($table);
        }

        return false;
    }

    /**
     * Get the affected rows
     */
    public function affected_rows()
    {
        return $this->db->affectedRows();
    }

    /**
     * Get the last insert id
     */
    public function insert_id()
    {
        return $this->db->insertID();
    }

    /**
     * Getting the last executed query
     */
    public function last_query()
    {
        return $this->db->getLastQuery();
    }

    /**
     * Run the SQL command string
     *
     * @param   mixed|null $query
     */
    public function query($query = null, $params = [], $return = false)
    {
        // Convert multiple line to single line
        $query = trim(preg_replace('/\s+/S', ' ', $query));

        // Remove string inside bracket to extract the primary table
        $extract_table = preg_replace('/\(([^()]*+|(?R))*\)/', '', $query);

        // Get primary table
        preg_match('/FROM[\s]+(.*?)[\s]+/i', $extract_table, $matches);

        if (isset($matches[1])) {
            // Primary table found
            $this->_table = trim(str_replace(['`', '"', '\''], '', $matches[1]));
        }

        if ($return) {
            // Returning the query
            return $this->db->query($query, $params);
        }

        $this->_prepare[] = [
            'function' => 'query',
            'arguments' => [$query, $params]
        ];

        $this->_is_query = true;

        return $this;
    }

    /**
     * Distinct field
     */
    public function distinct($flag = true)
    {
        $this->_prepare[] = [
            'function' => 'distinct',
            'arguments' => [$flag]
        ];

        return $this;
    }

    /**
     * Select field
     * Possible to use comma separated
     *
     * @param   mixed|null $column
     */
    public function select($column = null, $escape = true)
    {
        $this->_selection = true;

        if (! is_array($column)) {
            // Split selected by comma, but ignore which is inside brackets
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        $column = array_unique($column);

        $this->_prepare[] = [
            'function' => 'select',
            'arguments' => [$column, $escape]
        ];

        return $this;
    }

    /**
     * Select and count
     *
     * @param   mixed|null $column
     * @param   mixed|null $alias
     */
    public function select_count($column = null, $alias = null)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectCount',
            'arguments' => [$column, $alias]
        ];

        return $this;
    }

    /**
     * Select and sum
     *
     * @param   mixed|null $column
     * @param   mixed|null $alias
     */
    public function select_sum($column = null, $alias = null)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectSum',
            'arguments' => [$column, $alias]
        ];

        return $this;
    }

    /**
     * Select minimum
     *
     * @param   mixed|null $column
     * @param   mixed|null $alias
     */
    public function select_min($column = null, $alias = null)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectMin',
            'arguments' => [$column, $alias]
        ];

        return $this;
    }

    /**
     * Select maximum
     *
     * @param   mixed|null $column
     * @param   mixed|null $alias
     */
    public function select_max($column = null, $alias = null)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectMax',
            'arguments' => [$column, $alias]
        ];

        return $this;
    }

    /**
     * Select the average of field
     *
     * @param   mixed|null $column
     * @param   mixed|null $alias
     */
    public function select_avg($column = null, $alias = null)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectAvg',
            'arguments' => [$column, $alias]
        ];

        return $this;
    }

    /**
     * Select subqueries
     *
     * @param   mixed|null $subquery
     * @param   mixed|null $alias
     */
    public function select_subquery($subquery, string $alias)
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectSubquery',
            'arguments' => [$subquery, $alias]
        ];

        return $this;
    }

    /**
     * Set the primary table
     *
     * @param   mixed|null $table
     */
    public function from($table = null)
    {
        $this->_table = $table;

        $this->_builder = $this->db->table($table);

        return $this;
    }

    /**
     * From subqueries
     *
     * @param   mixed|null $subquery
     * @param   mixed|null $alias
     */
    public function from_subquery($subquery, string $alias)
    {
        $this->_prepare[] = [
            'function' => 'fromSubquery',
            'arguments' => [$subquery, $alias]
        ];

        return $this;
    }

    /**
     * New query
     */
    public function new_query()
    {
        $this->_prepare[] = [
            'function' => 'newQuery',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Set the primary table
     * It's aliased to from() method
     *
     * @param   mixed|null $table
     */
    public function table($table = null)
    {
        $this->_table = $table;

        $this->_builder = $this->db->table($table);

        return $this;
    }

    /**
     * Your contribution is needed to write hint about
     * this method
     *
     * @param   mixed|null $table
     * @param   mixed|null $condition
     */
    public function join($table = null, $condition = null, $type = '', $escape = true)
    {
        $this->_prepare[] = [
            'function' => 'join',
            'arguments' => [$table, $condition, $type, $escape]
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function where($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            // Run where command
            foreach ($field as $key => $val) {
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'where',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
                ];
            }
        } else {
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'where',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_where($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            // Run or where command
            foreach ($field as $key => $val) {
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'orWhere',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
                ];
            }
        } else {
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'orWhere',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function where_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'whereIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'whereIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_where_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orWhereIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orWhereIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function where_not_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'whereNotIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'whereNotIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_where_not_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orWhereNotIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orWhereNotIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'like',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function not_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'notLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_not_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orNotLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            // Run having command
            foreach ($field as $key => $val) {
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'having',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
                ];
            }
        } else {
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'having',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_having($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            // Run or having command
            foreach ($field as $key => $val) {
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'orHaving',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
                ];
            }
        } else {
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'orHaving',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'havingIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'havingIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_having_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orHavingIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orHavingIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having_not_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'havingNotIn',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'havingNotIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_having_not_in($field = '', $value = '', $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orHavingNotIN',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orHavingNotIn',
                'arguments' => [$field, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val);

            $this->_prepare[] = [
                'function' => 'havingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val);

            $this->_prepare[] = [
                'function' => 'orHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val);

            $this->_prepare[] = [
                'function' => 'notHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
    {
        $column = [];

        if (! is_array($field)) {
            if (isset($match['match'])) {
                $column[$field] = ($match ? $match : '');
            } else {
                $column[$field] = [
                    'match' => ($match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive
                ];
            }
        }

        foreach ($column as $key => $val) {
            $cast = $this->_cast_column($key, $val);

            $this->_prepare[] = [
                'function' => 'orNotHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['side'], $val['escape'], $val['case_insensitive']]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $column
     */
    public function group_by($column = null)
    {
        if (in_array($this->db->DBDriver, ['SQLSRV'])) {
            $column = array_map('trim', explode(',', $column));

            // Loops the group list
            foreach ($column as $key => $val) {
                if (stripos($val, '(') && stripos($val, ')')) {
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        'arguments' => [$val]
                    ];
                } else {
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        'arguments' => ['CONVERT(VARCHAR(MAX), ' . (stripos($val, ' AS ') !== false ? substr($val, 0, stripos($val, ' AS ')) : $val) . ')']
                    ];
                }
            }
        } else {
            $this->_prepare[] = [
                'function' => 'groupBy',
                'arguments' => [$column]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $column
     */
    public function order_by($column = null, $direction = '', $escape = true)
    {
        $this->_ordered = true;

        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orderBy',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } elseif ($direction) {
            $this->_prepare[] = [
                'function' => 'orderBy',
                'arguments' => [$column, $direction, $escape]
            ];
        } else {
            $column = ($column ? array_map('trim', preg_split('/,(?![^(]+\))/', trim($column))) : []);

            foreach ($column as $key => $val) {
                $dir = '';

                if (strpos($val, '(') !== false && strpos($val, ')') !== false) {
                    $col = $val;
                } else {
                    list($col, $dir) = array_pad(array_map('trim', explode(' ', $val)), 2, '');
                }

                $this->_prepare[] = [
                    'function' => 'orderBy',
                    'arguments' => [$col, $dir, $escape]
                ];
            }
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function limit($limit = 0, $offset = 0)
    {
        $this->_prepare[] = [
            'function' => 'limit',
            'arguments' => [$limit, $offset]
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $offset
     */
    public function offset($offset = null)
    {
        $this->_prepare[] = [
            'function' => 'offset',
            'arguments' => [$offset]
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function group_start()
    {
        $this->_prepare[] = [
            'function' => 'groupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_group_start()
    {
        $this->_prepare[] = [
            'function' => 'orGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function not_group_start()
    {
        $this->_prepare[] = [
            'function' => 'notGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_not_group_start()
    {
        $this->_prepare[] = [
            'function' => 'orNotGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function group_end()
    {
        $this->_prepare[] = [
            'function' => 'groupEnd',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having_group_start()
    {
        $this->_prepare[] = [
            'function' => 'havingGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_having_group_start()
    {
        $this->_prepare[] = [
            'function' => 'orHavingGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function not_having_group_start()
    {
        $this->_prepare[] = [
            'function' => 'notHavingGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function or_not_having_group_start()
    {
        $this->_prepare[] = [
            'function' => 'orNotHavingGroupStart',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function having_group_end()
    {
        $this->_prepare[] = [
            'function' => 'havingGroupEnd',
            'arguments' => []
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function get($table = null, $limit = 0, $offset = 0)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($limit && (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10))) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        $this->_prepare[] = [
            'function' => 'get',
            'arguments' => [$limit, $offset]
        ];

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     * @param   mixed|null $offset
     */
    public function get_where($table = null, array $where = [], $limit = 0, $offset = null, $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($limit && (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10))) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        if ($where && 'Postgre' == $this->db->DBDriver) {
            foreach ($where as $key => $val) {
                $cast = $this->_cast_column($key, $val);

                $where[$cast['column']] = $cast['value'];

                if ($key != $cast['column']) {
                    unset($where[$key]);
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'getWhere',
            'arguments' => [$where, $limit, $offset, $reset]
        ];

        return $this;
    }

    /**
     * Breaks query builder
     */
    public function reset_query()
    {
        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function result()
    {
        $this->_prepare[] = [
            'function' => 'getResultObject',
            'arguments' => []
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function result_array()
    {
        $this->_prepare[] = [
            'function' => 'getResultArray',
            'arguments' => []
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function row($field = 1)
    {
        if (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10)) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => (is_int($field) ? 'getRowObject' : 'getRow'),
            'arguments' => [$field]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function row_array($field = 1)
    {
        if (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10)) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => 'getRowArray',
            'arguments' => [$field]
        ];

        return $this->_run_query();
    }

    /**
     * Get the number of rows
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function num_rows($table = null, $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'getNumRows',
            'arguments' => [$reset]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function count_all($table = null, $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAll',
            'arguments' => [$reset]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function count_all_results($table = null, $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAllResults',
            'arguments' => [$reset]
        ];

        return $this->_run_query();
    }

    /**
     * Implement set preparation to insert or update data
     *
     * @param   mixed|null $value
     */
    public function set($column, $value = null, $escape = true)
    {
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'set',
                    'arguments' => [$key, $val, $escape]
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'set',
                'arguments' => [$column, $value, $escape]
            ];
        }

        return $this;
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function insert($table = null, $set = [], $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            $set = array_merge($this->_set, $set);
        }

        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if ('PRIMARY' == $val->type) {
                        $set[$val->fields[0]] = ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);

                        break;
                    }
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'insert',
            'arguments' => [$set, $escape]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function insert_batch($table = null, $set = [], $batch_size = 1, $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $set = array_merge($this->_set, $set);

        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if ('PRIMARY' == $val->type) {
                        $primary = $val->fields[0];
                        $auto_increment = ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);

                        break;
                    }
                }
            }

            $new_set = [];

            foreach ($set as $key => $val) {
                foreach ($val as $_key => $_val) {
                    $_val[$primary] = $auto_increment;
                    $val = $_val;

                    $auto_increment++;
                }

                $new_set[] = $val;
            }

            $set = $new_set;
        }

        $this->_prepare[] = [
            'function' => 'insertBatch',
            'arguments' => [$set, $escape, $batch_size]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     * @param   mixed|null $limit
     */
    public function update($table = null, $set = [], array $where = [], $limit = null)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($limit && (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10))) {
            $this->_limit = $limit;
        }

        $set = array_merge($this->_set, $set);

        foreach ($where as $key => $val) {
            if (is_array($val) && isset($val['value'])) {
                $where[$key] = $val['value'];
            } else {
                $where[$key] = $val;
            }
        }

        $this->_prepare[] = [
            'function' => 'update',
            'arguments' => [$set, $where, (! in_array($this->db->DBDriver, ['Postgre', 'SQLite3']) ? $this->_limit : null)]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function update_batch($table = null, $set = [], $batch_size = 1, $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($set) {
            $set = array_merge($this->_set, $set);
        }

        $this->_prepare[] = [
            'function' => 'updateBatch',
            'arguments' => [$set, '', $batch_size]
        ];

        return $this->_run_query();
    }

    /**
     * Update data or insert if record is not exists
     *
     * @param   mixed|null $table
     */
    public function upsert($table = null, $set = [], $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            $set = array_merge($this->_set, $set);
        }

        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if ('PRIMARY' == $val->type) {
                        $set[$val->fields[0]] = ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);

                        break;
                    }
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'upsert',
            'arguments' => [$set, $escape]
        ];

        return $this->_run_query();
    }

    /**
     * Batch update data or insert if record is not exists
     *
     * @param   mixed|null $table
     */
    public function upsert_batch($table = null, $set = [], $batch_size = 1, $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $set = array_merge($this->_set, $set);

        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if ('PRIMARY' == $val->type) {
                        $primary = $val->fields[0];
                        $auto_increment = ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);

                        break;
                    }
                }
            }

            $new_set = [];

            foreach ($set as $key => $val) {
                foreach ($val as $_key => $_val) {
                    $_val[$primary] = $auto_increment;
                    $val = $_val;

                    $auto_increment++;
                }

                $new_set[] = $val;
            }

            $set = $new_set;
        }

        $this->_prepare[] = [
            'function' => 'upsertBatch',
            'arguments' => [$set, $escape, $batch_size]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function replace($table = null, $set = [])
    {
        if ($set) {
            $set = array_merge($this->_set, $set);
        }

        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'replace',
            'arguments' => [$set]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function delete($table = null, $where = [], $limit = 0, $reset_data = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($limit && (! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && $this->db->getVersion() >= 10))) {
            $this->_limit = $limit;
        }

        $this->_prepare[] = [
            'function' => 'delete',
            'arguments' => [$where, (! in_array($this->db->DBDriver, ['Postgre']) ? $this->_limit : null)]
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function truncate($table = null)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'truncate',
            'arguments' => []
        ];

        return $this->_run_query();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @param   mixed|null $table
     */
    public function empty_table($table = null)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'emptyTable',
            'arguments' => []
        ];

        return $this->_run_query();
    }

    /**
     * Transaction Begin
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_begin()
    {
        $this->db->transBegin();

        return $this;
    }

    /**
     * Transaction Start
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_start()
    {
        $this->db->transStart();

        return $this;
    }

    /**
     * Transaction Complete
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_complete()
    {
        return $this->db->transComplete();
    }

    /**
     * Get Transaction Status
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_status()
    {
        return $this->db->transStatus();
    }

    /**
     * Transaction Commit
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_commit()
    {
        return $this->db->transCommit();
    }

    /**
     * Transaction Rolling Back
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_rollback()
    {
        return $this->db->transRollback();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     */
    public function error()
    {
        return $this->db->error();
    }

    /**
     * Run the query of collected property
     * Your contribution is needed to write complete hint about this method
     */
    private function _run_query()
    {
        if (! $this->_builder) {
            if ($this->_is_query) {
                $this->_builder = $this->db;
            } else {
                $this->_builder = $this->db->table($this->_table);

                if ($this->_limit) {
                    $this->_builder->limit($this->_limit, $this->_offset);
                }

                if (! $this->_selection) {
                    $this->_builder->select('*');
                }
            }
        }

        $query = [];

        $builder_filter = ['get', 'getWhere', 'countAll', 'countAllResults', 'insert', 'insertBatch', 'update', 'updateBatch', 'delete', 'deleteBatch', 'truncate', 'emptyTable', 'query', 'selectSubquery', 'fromSubquery'];
        $result_filter = ['getFieldCount', 'getFieldName', 'getFieldData', 'getNumRows', 'getResult', 'getResultArray', 'getResultObject', 'getRow', 'getRowArray', 'getRowObject'];
        $row_request = false;

        foreach ($this->_prepare as $key => $val) {
            $function = $val['function'];
            $arguments = $val['arguments'];

            if (in_array($function, $builder_filter)) {
                if ('selectSubquery' == $function) {
                    if ($query) {
                        // Hacking line
                        $reflectionClass = new \ReflectionClass(($query));

                        // Get query builder select list
                        $reflectionProperty = $reflectionClass->getProperty('QBSelect');

                        // Set property accessible (only required prior to PHP 8.1.0)
                        $reflectionProperty->setAccessible(true);

                        // Modify not unique select value
                        $reflectionProperty->setValue(($query), array_unique($reflectionProperty->getValue(($query))));

                        // Select subquery
                        $this->_builder = $this->db->table($this->_table)->selectSubquery($query, $arguments[1]);
                    }

                    continue;
                } elseif ('fromSubquery' == $function) {
                    if ($query) {
                        // Select from subquery
                        $this->_builder = $this->db->newQuery()->fromSubquery($query, $arguments[1]);
                    }

                    continue;
                }

                $this->_get = true;

                // Indicates that query builder has finished
                $this->_finished = true;
            } elseif (in_array($function, $result_filter)) {
                if (! $this->_get) {
                    $this->_builder = $this->_builder->get();
                } elseif (isset($query)) {
                    $this->_builder = $query;
                }

                // Indicates that query builder has finished
                $this->_finished = true;
            }

            if (! method_exists($this->_builder, $function)) {
                continue;
            }

            if (is_array($arguments) && sizeof($arguments) == 7) {
                $query = $this->_builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
            } elseif (is_array($arguments) && sizeof($arguments) == 6) {
                $query = $this->_builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
            } elseif (is_array($arguments) && sizeof($arguments) == 5) {
                $query = $this->_builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            } elseif (is_array($arguments) && sizeof($arguments) == 4) {
                $query = $this->_builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            } elseif (is_array($arguments) && sizeof($arguments) == 3) {
                $query = $this->_builder->$function($arguments[0], $arguments[1], $arguments[2]);
            } elseif (is_array($arguments) && sizeof($arguments) == 2) {
                $query = $this->_builder->$function($arguments[0], $arguments[1]);
            } else {
                $query = $this->_builder->$function((isset($arguments[0]) ? $arguments[0] : $arguments));
            }
        }

        if ($this->_finished) {
            // Reset properties
            $this->_builder = null;
            $this->_prepare = [];
            $this->_finished = false;
            $this->_ordered = false;
            $this->_from = null;
            $this->_table = null;
            $this->_set = [];
            $this->_limit = null;
            $this->_offset = null;
            $this->_set = [];
            $this->_get = false;
            $this->_is_query = false;

            return $query;
        }

        return $this;
    }

    /**
     * Casting the column
     *
     * @param   null|mixed $column
     */
    private function _cast_column($column = null, $value = '')
    {
        $column = trim($column);
        $operand = null;
        $escape = true;

        if (strpos($column, ' ') !== false) {
            // Get operand if any
            $get_operand = substr($column, strpos($column, ' ') + 1);

            if (in_array($get_operand, ['!=', '>=', '<=', '>', '<'])) {
                // Remove operand from column
                $column = substr($column, 0, strpos($column, ' '));

                // Set operand
                $operand = $get_operand;
            } elseif (in_array(strtoupper($get_operand), ['IS NULL', 'IS NOT NULL'])) {
                // Remove operand from column
                $column = substr($column, 0, strpos($column, ' '));

                // Set operand
                $operand = $get_operand;

                // Set escape
                $escape = false;
            }
        }

        if (in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) && ! stripos($column, '(') && ! stripos($column, ')')) {
            // Type casting for PostgreSQL
            if (in_array(gettype($value), ['integer'])) {
                $cast_type = 'INTEGER';
                $value = (int) $value;
            } elseif (in_array(gettype($value), ['double'])) {
                $cast_type = 'DOUBLE';
                $value = (float) $value;
            } elseif (in_array(gettype($value), ['float'])) {
                $cast_type = 'FLOAT';
                $value = (float) $value;
            } elseif ($value && \DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
                $cast_type = ('SQLSRV' == $this->db->DBDriver ? 'DATETIME' : 'TIMESTAMP');
                $value = (string) $value;
            } elseif ($value && \DateTime::createFromFormat('Y-m-d', $value)) {
                $cast_type = 'DATE';
                $value = (string) $value;
            } elseif (! is_array(gettype($value))) {
                $cast_type = 'VARCHAR' . ('SQLSRV' == $this->db->DBDriver ? '(MAX)' : null);
                $value = (string) $value;
            }

            $column = (stripos($column, ' ') !== false ? substr($column, 0, stripos($column, ' ')) : $column);

            if ('SQLSRV' == $this->db->DBDriver) {
                $column = 'CONVERT(' . $cast_type . ', ' . $column . ')';
            } else {
                $column = 'CAST(' . $column . ' AS ' . $cast_type . ')';
            }

            if (strpos($cast_type, 'VARCHAR') !== false) {
                $column = 'LOWER(' . $column . ')';
                $value = strtolower($value);
            }
        }

        return [
            'column' => $column . ($operand ? ' ' . $operand : null),
            'value' => $value,
            'escape' => $escape
        ];
    }
}
