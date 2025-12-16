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

use Config\Services;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Query;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Database\BaseBuilder;

/**
 * Class Model
 *
 * Provides a database abstraction layer built on top of CodeIgniter's Database Connection
 * and Query Builder, with enhancements for various database operations and configurations.
 */
class Model
{
    /**
     * @var bool Flag to check if a different database config was called.
     */
    private bool $_called = false;

    /**
     * @var bool Flag to check if the query building process is finished and ready to execute/return.
     */
    private bool $_finished = false;

    /**
     * @var string|null Stores the table name set by the `from()` method.
     */
    private ?string $_from = null;

    /**
     * @var bool Flag to check if a retrieval method (`get`, `getWhere`) was called.
     */
    private bool $_get = false;

    /**
     * @var bool Flag to check if a raw `query()` method was called.
     */
    private bool $_is_query = false;

    /**
     * @var int|null Stores the limit value for the query.
     */
    private ?int $_limit = null;

    /**
     * @var int|null Stores the offset value for the query.
     */
    private ?int $_offset = null;

    /**
     * @var bool Flag to check if `order_by()` was called.
     */
    private bool $_ordered = false;

    /**
     * @var array<int, array<string, mixed>> Array to store chained query builder calls.
     */
    private array $_prepare = [];

    /**
     * @var bool Flag to check if a `select()` method was called.
     */
    private bool $_selection = false;

    /**
     * @var array<string, mixed> Array to store set values for insert/update operations.
     */
    private array $_set = [];

    /**
     * @var string|null Stores the primary table name for the query.
     */
    private ?string $_table = null;

    /**
     * @var BaseBuilder|BaseConnection|null Holds the CodeIgniter Query Builder or Database Connection instance.
     */
    private BaseBuilder|BaseConnection|null $_builder = null;

    /**
     * @var BaseConnection The active database connection instance.
     */
    private BaseConnection $db;

    /**
     * Class constructor.
     * Initializes the default database connection.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Use third party database on the fly or connect to a configured connection.
     *
     * @param string|int|array<string, mixed>|null $driver The connection name (string or int for database ID) or an array of connection settings. Null for default connection reset.
     * @param string|null $hostname Database hostname.
     * @param int|null $port Database port.
     * @param string|null $username Database username.
     * @param string|null $password Database password.
     * @param string|null $database Database name.
     * @return $this|false Returns the current object instance on success, or false on reset failure.
     * @throws Throwable
     */
    public function database_config(string|int|array|null $driver = null, ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null): self|false
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
                    $builder->where('year', (function_exists('get_userdata') && get_userdata('year') ? get_userdata('year') : date('Y')));
                } else {
                    $builder->where('id', $driver);
                }

                /** @var object|null $parameter */
                $parameter = $builder->getWhere(
                    [
                        'status' => 1,
                    ],
                    1
                )
                ->getRow();

                if (null === $parameter) {
                    return function_exists('throw_exception') ? throw_exception(404, 'The database connection could not be found.') : false;
                }

                $encrypter = Services::encrypter();

                $config = [
                    'DBDriver' => $parameter->database_driver,
                    'hostname' => $parameter->hostname,
                    'port' => $port,
                    'username' => function_exists('service') ? $encrypter->decrypt(base64_decode($parameter->username)) : $parameter->username,
                    'password' => function_exists('service') ? $encrypter->decrypt(base64_decode($parameter->password)) : $parameter->password,
                    'database' => $parameter->database_name,
                    'DBDebug' => (defined('ENVIRONMENT') && ENVIRONMENT !== 'production'),
                ];

                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($config);

                // Try to initialize the connection
                $this->db->initialize();

                $this->_called = true;

                // Store environment variables
                $_ENV['DBDriver'] = $config['DBDriver'];
                $_ENV['hostname'] = $config['hostname'];
                $_ENV['port'] = $config['port'] ?? null;
                $_ENV['username'] = $config['username'];
                $_ENV['password'] = $config['password'];
                $_ENV['database'] = $config['database'];
                $_ENV['DBDebug'] = (defined('ENVIRONMENT') && ENVIRONMENT !== 'production');
            } catch (\Throwable $e) {
                // Decrypt error
                // Assuming throw_exception is a defined global function
                return function_exists('throw_exception') ? throw_exception(403, $e->getMessage()) : false;
            }
        } elseif (is_array($driver) && isset($driver['DBDriver']) && isset($driver['hostname']) && isset($driver['username']) && isset($driver['database'])) {
            try {
                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($driver);

                // Try to initialize the connection
                $this->db->initialize();
            } catch (\Throwable $e) {
                return function_exists('throw_exception') ? throw_exception(403, $e->getMessage()) : false;
            }
        } elseif (is_string($driver) && $hostname && $username && $database) {
            $config = [
                'DBDriver' => $driver,
                'hostname' => $hostname,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'database' => $database,
                'DBDebug' => (defined('ENVIRONMENT') && ENVIRONMENT !== 'production'),
            ];

            try {
                // Initialize parameter to new connection
                $this->db = \Config\Database::connect($config);

                // Try to initialize the connection
                $this->db->initialize();

                // Store environment variables
                $_ENV['DBDriver'] = $config['DBDriver'];
                $_ENV['hostname'] = $config['hostname'];
                $_ENV['port'] = $config['port'] ?? null;
                $_ENV['username'] = $config['username'];
                $_ENV['password'] = $config['password'];
                $_ENV['database'] = $config['database'];
                $_ENV['DBDebug'] = (defined('ENVIRONMENT') && ENVIRONMENT !== 'production');
            } catch (\Throwable $e) {
                return function_exists('throw_exception') ? throw_exception(403, $e->getMessage()) : false;
            }
        }

        return $this;
    }

    /**
     * Get the database driver.
     */
    public function db_driver(): string
    {
        return $this->db->DBDriver;
    }

    /**
     * Escape string
     */
    public function escape($string): string
    {
        return $this->db->escape($string);
    }

    /**
     * Disable foreign key check for truncating the table.
     */
    public function disable_foreign_key(): void
    {
        $this->db->disableForeignKeyChecks();
    }

    /**
     * Enable foreign key check for truncating the table.
     */
    public function enable_foreign_key(): void
    {
        $this->db->enableForeignKeyChecks();
    }

    /**
     * List available tables on current active database.
     *
     * @return array<int, string>
     */
    public function list_tables(): array
    {
        return $this->db->listTables();
    }

    /**
     * Check the existence of a table on the current active database.
     *
     * @param string $table The table name.
     */
    public function table_exists(string $table): bool
    {
        if ($table && $this->db->tableExists($table)) {
            return true;
        }

        return false;
    }

    /**
     * Check the field existence of a selected table.
     *
     * @param string $field The field name.
     * @param string $table The table name (can include an alias).
     */
    public function field_exists(string $field, string $table): bool
    {
        if (strpos(trim($table), '(') !== false || strpos(strtolower(trim($table)), 'select ') !== false) {
            return false;
        }

        // Store alias for later use (though $_table_alias is not defined in properties, following original logic)
        $temp_table_alias = [];
        if (strpos(trim($table), ' ') !== false) {
            $table = str_ireplace(' AS ', ' ', $table);
            $destructure = explode(' ', $table);
            $table = $destructure[0];

            $temp_table_alias[$destructure[1]] = $table; // This variable is local now
        }

        if ($table && $field && $this->db->tableExists($table) && $this->db->fieldExists($field, $table)) {
            return true;
        }

        return false;
    }

    /**
     * List the fields of a selected table.
     *
     * @param string $table The table name.
     * @return array<int, string>|false Array of field names on success, false otherwise.
     */
    public function list_fields(string $table): array|false
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getFieldNames($table);
        }

        return false;
    }

    /**
     * Get the table metadata and field info of a selected table.
     *
     * @param string $table The table name.
     * @return array<int, \CodeIgniter\Database\FieldData>|false Array of field data objects on success, false otherwise.
     */
    public function field_data(string $table): array|false
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getFieldData($table);
        }

        return false;
    }

    /**
     * Get the table index data of a selected table.
     *
     * @param string $table The table name.
     * @return array<string, \CodeIgniter\Database\IndexData>|false Array of index data objects on success, false otherwise.
     */
    public function index_data(string $table): array|false
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getIndexData($table);
        }

        return false;
    }

    /**
     * Get the table foreign key data of a selected table.
     *
     * @param string $table The table name.
     * @return array<string, \CodeIgniter\Database\ForeignKeyData>|false Array of foreign key data objects on success, false otherwise.
     */
    public function foreign_data(string $table): array|false
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getForeignKeyData($table);
        }

        return false;
    }

    /**
     * Get the number of affected rows by the last query.
     */
    public function affected_rows(): int
    {
        return $this->db->affectedRows();
    }

    /**
     * Get the ID generated by the last insert statement.
     */
    public function insert_id(): int|string
    {
        return $this->db->insertID();
    }

    /**
     * Getting the last executed query.
     */
    public function last_query(): Query|string
    {
        return $this->db->getLastQuery();
    }

    /**
     * Run the SQL command string.
     *
     * @param string $query The raw SQL query string.
     * @param array<int|string, mixed> $params Array of parameters to bind to the query.
     * @param bool $return If true, the query result object is returned immediately.
     * @return $this|ResultInterface
     */
    public function query(string $query, array $params = [], bool $return = false)
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
            'arguments' => [$query, $params],
        ];

        $this->_is_query = true;

        return $this;
    }

    /**
     * Create a new subquery builder instance.
     * Creates a new instance for subquery so it does not conflict with the main query.
     *
     * @param string|null $table Optional table name.
     */
    public function subquery(?string $table = null): self
    {
        // Create a new instance of the same class
        $subquery = new self();

        // Pass database connection to the subquery instance
        $subquery->db = $this->db;

        if ($table) {
            // Use from() to set the table
            return $subquery->from($table);
        }

        return $subquery;
    }

    /**
     * Enables or disables the DISTINCT clause.
     *
     * @param bool $flag Whether to use DISTINCT (true) or not (false).
     * @return $this
     */
    public function distinct(bool $flag = true): self
    {
        $this->_prepare[] = [
            'function' => 'distinct',
            'arguments' => [$flag],
        ];

        return $this;
    }

    /**
     * Select field(s). Possible to use comma separated or an array.
     *
     * @param string|array<int|string, string> $column The column(s) to select.
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function select(string|array $column, bool $escape = true): self
    {
        $this->_selection = true;

        if (! is_array($column)) {
            // Split selected by comma, but ignore what is inside brackets
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        // Filter out duplicates
        $column = array_unique($column);

        $this->_prepare[] = [
            'function' => 'select',
            'arguments' => [$column, $escape],
        ];

        return $this;
    }

    /**
     * Select and count a column.
     *
     * @param string $column The column to count.
     * @param string|null $alias Optional alias for the counted column.
     * @return $this
     */
    public function select_count(string $column, ?string $alias = null): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectCount',
            'arguments' => [$column, $alias],
        ];

        return $this;
    }

    /**
     * Select and sum a column.
     *
     * @param string $column The column to sum.
     * @param string|null $alias Optional alias for the summed column.
     * @return $this
     */
    public function select_sum(string $column, ?string $alias = null): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectSum',
            'arguments' => [$column, $alias],
        ];

        return $this;
    }

    /**
     * Select minimum value of a column.
     *
     * @param string $column The column to find the minimum value of.
     * @param string|null $alias Optional alias for the resulting column.
     * @return $this
     */
    public function select_min(string $column, ?string $alias = null): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectMin',
            'arguments' => [$column, $alias],
        ];

        return $this;
    }

    /**
     * Select maximum value of a column.
     *
     * @param string $column The column to find the maximum value of.
     * @param string|null $alias Optional alias for the resulting column.
     * @return $this
     */
    public function select_max(string $column, ?string $alias = null): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectMax',
            'arguments' => [$column, $alias],
        ];

        return $this;
    }

    /**
     * Select the average value of a field.
     *
     * @param string $column The column to average.
     * @param string|null $alias Optional alias for the resulting column.
     * @return $this
     */
    public function select_avg(string $column, ?string $alias = null): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectAvg',
            'arguments' => [$column, $alias],
        ];

        return $this;
    }

    /**
     * Select subqueries.
     *
     * @param self $subquery The subquery object.
     * @param string $alias The alias for the subquery result.
     * @return $this
     */
    public function select_subquery(self $subquery, string $alias): self
    {
        $this->_selection = true;

        $this->_prepare[] = [
            'function' => 'selectSubquery',
            'arguments' => [$subquery, $alias],
        ];

        return $this;
    }

    /**
     * Set the primary table for the query.
     *
     * @param string $table The table name.
     * @return $this
     */
    public function from(string $table): self
    {
        $this->_table = $table;

        $this->_builder = $this->db->table($table);

        return $this;
    }

    /**
     * Use a subquery as the primary table.
     *
     * @param self $subquery The subquery object.
     * @param string $alias The alias for the subquery table.
     * @return $this
     */
    public function from_subquery(self $subquery, string $alias): self
    {
        $this->_prepare[] = [
            'function' => 'fromSubquery',
            'arguments' => [$subquery, $alias],
        ];

        return $this;
    }

    /**
     * Starts a new query, resetting all existing WHERE, JOIN, and SELECT clauses.
     *
     * @return $this
     */
    public function new_query(): self
    {
        $this->_prepare[] = [
            'function' => 'newQuery',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Set the primary table.
     * It's aliased to from() method.
     *
     * @param string $table The table name.
     * @return $this
     */
    public function table(string $table): self
    {
        $this->from($table);

        return $this;
    }

    /**
     * Adds a JOIN clause to the query.
     *
     * @param string $table The table to join.
     * @param string $condition The join condition (e.g., 'table1.id = table2.table1_id').
     * @param string $type The type of join ('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', etc.).
     * @param bool $escape Whether to escape table/column names.
     * @return $this
     */
    public function join(string $table, string $condition, string $type = '', bool $escape = true): self
    {
        $this->_prepare[] = [
            'function' => 'join',
            'arguments' => [$table, $condition, $type, $escape],
        ];

        return $this;
    }

    /**
     * Generates a WHERE clause in the query.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $value The field value (if $field is a string).
     * @param bool $escape Whether to escape the field and value.
     * @return $this
     */
    public function where(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run where command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'where',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'where',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR WHERE clause in the query.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $value The field value (if $field is a string).
     * @param bool $escape Whether to escape the field and value.
     * @return $this
     */
    public function or_where(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run or where command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'orWhere',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'orWhere',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
            ];
        }

        return $this;
    }

    /**
     * Generates a WHERE IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to check against (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function where_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'whereIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'whereIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR WHERE IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to check against (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function or_where_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orWhereIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orWhereIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates a WHERE NOT IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to exclude (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function where_not_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'whereNotIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'whereNotIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR WHERE NOT IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to exclude (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function or_where_not_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orWhereNotIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orWhereNotIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates a LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'like',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function or_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates a NOT LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function not_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'notLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR NOT LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function or_not_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orNotLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates a HAVING clause in the query.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $value The field value (if $field is a string).
     * @param bool $escape Whether to escape the field and value.
     * @return $this
     */
    public function having(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run having command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'having',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'having',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR HAVING clause in the query.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $value The field value (if $field is a string).
     * @param bool $escape Whether to escape the field and value.
     * @return $this
     */
    public function or_having(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run or having command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_cast_column($key, $val);

                $this->_prepare[] = [
                    'function' => 'orHaving',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($field, $value);

            $this->_prepare[] = [
                'function' => 'orHaving',
                'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
            ];
        }

        return $this;
    }

    /**
     * Generates a HAVING IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to check against (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function having_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'havingIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'havingIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR HAVING IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to check against (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function or_having_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orHavingIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orHavingIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates a HAVING NOT IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to exclude (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function having_not_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'havingNotIn',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'havingNotIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR HAVING NOT IN clause.
     *
     * @param string|array<string, array<int, mixed>> $field The field name or an associative array of conditions.
     * @param array<int, mixed>|null $value An array of values to exclude (if $field is a string).
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function or_having_not_in(string|array $field = '', ?array $value = null, bool $escape = true): self
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orHavingNotIN', // Note: original code has 'orHavingNotIN', likely a typo that should be 'orHavingNotIn'
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } else {
            $this->_prepare[] = [
                'function' => 'orHavingNotIn',
                'arguments' => [$field, $value, $escape],
            ];
        }

        return $this;
    }

    /**
     * Generates a HAVING LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function having_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'havingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR HAVING LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function or_having_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'] ?? false],
            ];
        }

        return $this;
    }

    /**
     * Generates a NOT HAVING LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function not_having_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'notHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive']],
            ];
        }

        return $this;
    }

    /**
     * Generates an OR NOT HAVING LIKE clause.
     *
     * @param string|array<string, mixed> $field The field name or an associative array of conditions.
     * @param mixed $match The value to match (if $field is a string). Can be string or array with 'match', 'side', 'escape', 'case_insensitive'.
     * @param string $side The placement of the wildcards ('before', 'after', 'both').
     * @param bool $escape Whether to escape the wildcard characters.
     * @param bool $case_insensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function or_not_having_like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): self
    {
        /** @var array<string, array<string, mixed>> $column */
        $column = [];

        if (! is_array($field)) {
            if (is_array($match) && isset($match['match'])) {
                $column[$field] = $match;
            } else {
                $column[$field] = [
                    'match' => (is_string($match) && $match ? $match : ''),
                    'side' => $side,
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $case_insensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_cast_column($key, $val['match']);

            $this->_prepare[] = [
                'function' => 'orNotHavingLike',
                'arguments' => [$cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'] ?? false],
            ];
        }

        return $this;
    }

    /**
     * Adds a GROUP BY clause to the query.
     *
     * @param string|array<int, string>|null $column The column(s) to group by. Can be a string of comma-separated column names or an array.
     * @return $this
     */
    public function group_by(string|array|null $column = null): self
    {
        if (in_array($this->db->DBDriver, ['SQLSRV'])) {
            $columns_array = is_string($column) ? array_map('trim', explode(',', $column)) : ($column ?? []);

            // Loops the group list
            foreach ($columns_array as $val) {
                if (stripos($val, '(') !== false && stripos($val, ')') !== false) {
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        'arguments' => [$val],
                    ];
                } else {
                    $column_name = (stripos($val, ' AS ') !== false ? substr($val, 0, stripos($val, ' AS ')) : $val);
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        // CodeIgniter 4 may handle this differently, adapting to original's intention for SQLSRV
                        'arguments' => ['CONVERT(VARCHAR(MAX), ' . $column_name . ')'],
                    ];
                }
            }
        } else {
            $this->_prepare[] = [
                'function' => 'groupBy',
                'arguments' => [$column],
            ];
        }

        return $this;
    }

    /**
     * Adds an ORDER BY clause to the query.
     *
     * @param string|array<string, string>|null $column The column(s) to order by. Can be a string, or an associative array with column => direction.
     * @param string $direction The ordering direction ('ASC' or 'DESC') if $column is a string.
     * @param bool $escape Whether to escape identifiers.
     * @return $this
     */
    public function order_by(string|array|null $column = null, string $direction = '', bool $escape = true): self
    {
        $this->_ordered = true;

        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'orderBy',
                    'arguments' => [$key, $val, $escape],
                ];
            }
        } elseif ($direction && is_string($column)) {
            $this->_prepare[] = [
                'function' => 'orderBy',
                'arguments' => [$column, $direction, $escape],
            ];
        } elseif (is_string($column)) {
            $columns_array = ($column ? array_map('trim', preg_split('/,(?![^(]+\))/', trim($column))) : []);

            foreach ($columns_array as $val) {
                $dir = '';
                $col = $val;

                if (strpos($val, '(') === false || strpos($val, ')') === false) {
                    // Split column and direction for standard 'col ASC/DESC' format
                    $parts = array_map('trim', explode(' ', $val));
                    $col = $parts[0];
                    $dir = $parts[1] ?? '';
                }
                // If it contains parentheses, it's likely a function call, and should be treated as $col = $val, $dir = ''

                $this->_prepare[] = [
                    'function' => 'orderBy',
                    'arguments' => [$col, $dir, $escape],
                ];
            }
        }

        return $this;
    }

    /**
     * Sets the LIMIT clause in the query.
     *
     * @param int $limit The maximum number of rows to return.
     * @param int|null $offset The offset from where to start fetching rows.
     * @return $this
     */
    public function limit(int $limit = 0, ?int $offset = null): self
    {
        $this->_prepare[] = [
            'function' => 'limit',
            'arguments' => [$limit, $offset],
        ];

        return $this;
    }

    /**
     * Sets the OFFSET clause in the query.
     *
     * @param int|null $offset The offset from where to start fetching rows.
     * @return $this
     */
    public function offset(?int $offset = null): self
    {
        $this->_prepare[] = [
            'function' => 'offset',
            'arguments' => [$offset],
        ];

        return $this;
    }

    /**
     * Starts a bracketed WHERE condition group (AND).
     *
     * @return $this
     */
    public function group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'groupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed WHERE condition group (OR).
     *
     * @return $this
     */
    public function or_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'orGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed WHERE NOT condition group (AND NOT).
     *
     * @return $this
     */
    public function not_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'notGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed WHERE NOT condition group (OR NOT).
     *
     * @return $this
     */
    public function or_not_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'orNotGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Ends a bracketed WHERE condition group.
     *
     * @return $this
     */
    public function group_end(): self
    {
        $this->_prepare[] = [
            'function' => 'groupEnd',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed HAVING condition group (AND).
     *
     * @return $this
     */
    public function having_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'havingGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed HAVING condition group (OR).
     *
     * @return $this
     */
    public function or_having_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'orHavingGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed HAVING NOT condition group (AND NOT).
     *
     * @return $this
     */
    public function not_having_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'notHavingGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Starts a bracketed HAVING NOT condition group (OR NOT).
     *
     * @return $this
     */
    public function or_not_having_group_start(): self
    {
        $this->_prepare[] = [
            'function' => 'orNotHavingGroupStart',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Ends a bracketed HAVING condition group.
     *
     * @return $this
     */
    public function having_group_end(): self
    {
        $this->_prepare[] = [
            'function' => 'havingGroupEnd',
            'arguments' => [],
        ];

        return $this;
    }

    /**
     * Compiles and runs a SELECT query.
     *
     * @param string $table The table name.
     * @param int $limit The maximum number of rows to return.
     * @param int $offset The offset from where to start fetching rows.
     * @return $this
     */
    public function get(string $table = '', int $limit = 0, int $offset = 0): self
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit/offset for non-SQLSRV/Postgre or SQLSRV version >= 10
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $is_db_limit_compatible) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        $this->_prepare[] = [
            // Only pass $limit and $offset if we rely on CI's get() to handle it, otherwise internal _run_query will handle limit/offset for pre-CI versions
            'function' => 'get',
            'arguments' => [$limit, $offset],
        ];

        return $this;
    }

    /**
     * Compiles and runs a SELECT query with a WHERE clause.
     *
     * @param string $table The table name.
     * @param array<string, mixed> $where An associative array of WHERE conditions.
     * @param int|null $limit The maximum number of rows to return.
     * @param int|null $offset The offset from where to start fetching rows.
     * @param bool $reset Whether to reset the query parameters after execution.
     * @return $this
     */
    public function get_where(string $table = '', array $where = [], ?int $limit = null, ?int $offset = null, bool $reset = true): self
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit/offset for non-SQLSRV/Postgre or SQLSRV version >= 10
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $is_db_limit_compatible) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        if ($where && 'Postgre' == $this->db->DBDriver) {
            foreach ($where as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_cast_column($key, $val);

                $where[$cast['column']] = $cast['value'];

                if ($key != $cast['column']) {
                    unset($where[$key]);
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'getWhere',
            'arguments' => [$where, $limit, $offset, $reset],
        ];

        return $this;
    }

    /**
     * Resets the query builder properties without running an execution command.
     * Useful for running a chain of commands and immediately resetting state without implicitly calling a result method.
     *
     * @return $this
     */
    public function reset_query(): self
    {
        $this->_run_query();

        // The _run_query will reset properties internally if it was marked as finished
        // or just return $this if it was not. We return $this either way to maintain chainability.
        return $this;
    }

    /**
     * Executes the query and returns the results as an array of objects.
     *
     * @return object<int, object>|ResultInterface
     */
    public function result()
    {
        $this->_prepare[] = [
            'function' => 'getResultObject',
            'arguments' => [],
        ];

        /** @var array<int, object>|ResultInterface $result */
        return $this->_run_query();
    }

    /**
     * Executes the query and returns the results as an array of arrays.
     *
     * @return array<int, array<string, mixed>>|ResultInterface
     */
    public function result_array()
    {
        $this->_prepare[] = [
            'function' => 'getResultArray',
            'arguments' => [],
        ];

        /** @var array<int, array<string, mixed>>|ResultInterface */
        return $this->_run_query();
    }

    /**
     * Executes the query and returns a single row as an object.
     *
     * @param int|string $field The row number to retrieve, or the field name to return directly.
     */
    public function row(int|string $field = 1)
    {
        // Apply limit for non-SQLSRV/Postgre or SQLSRV version >= 10 when retrieving a single row object.
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($is_db_limit_compatible) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => (is_int($field) ? 'getRowObject' : 'getRow'),
            'arguments' => [$field],
        ];

        /** @var object|string|int|float|bool|null */
        return $this->_run_query();
    }

    /**
     * Executes the query and returns a single row as an array.
     *
     * @param int|string $field The row number to retrieve, or the field name to return directly.
     * @return array<string, mixed>|string|int|float|bool|null
     */
    public function row_array(int|string $field = 1)
    {
        // Apply limit for non-SQLSRV/Postgre or SQLSRV version >= 10 when retrieving a single row array.
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($is_db_limit_compatible) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => 'getRowArray',
            'arguments' => [$field],
        ];

        return $this->_run_query();
    }

    /**
     * Get the number of rows from a query.
     *
     * @param string $table The table name (optional, only used if not set via `from()`/`table()`).
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function num_rows(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'getNumRows',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Counts all rows in the specified table.
     *
     * @param string $table The table name.
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function count_all(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAll',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Counts the rows of the last executed query result, respecting WHERE and other clauses.
     *
     * @param string $table The table name (optional, only used if not set via `from()`/`table()`).
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function count_all_results(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAllResults',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Implement set preparation to insert or update data
     *
     * @param string|array<string, mixed> $column The column name or an associative array of column => value pairs.
     * @param mixed $value The value for the column (if $column is a string).
     * @param bool $escape Whether to escape the value.
     * @return $this
     */
    public function set(string|array $column, mixed $value = null, bool $escape = true): self
    {
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->_prepare[] = [
                    'function' => 'set',
                    'arguments' => [$key, $val, $escape],
                ];
                $this->_set[$key] = $val; // Store also in _set for later use
            }
        } else {
            $this->_prepare[] = [
                'function' => 'set',
                'arguments' => [$column, $value, $escape],
            ];
            $this->_set[$column] = $value; // Store also in _set for later use
        }

        return $this;
    }

    /**
     * Inserts data into the database.
     *
     * @param string|null $table The table name (optional).
     * @param array<string, mixed> $set An associative array of data to insert.
     * @param bool $escape Whether to escape the data.
     */
    public function insert(?string $table = null, array $set = [], bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            $set = array_merge($this->_set, $set);
        }

        // SQLite3 Auto-Increment handling (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $index_data */
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    // Assuming $val is an object with 'type' and 'fields' properties
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary_field = $val->fields[0];
                        // Get max ID and increment
                        $max_id = $this->db->table($table)->selectMax($primary_field)->get()->getRow($primary_field) ?? 0;
                        $set[$primary_field] = (null !== $max_id ? $max_id + 1 : 1);

                        break;
                    }
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'insert',
            'arguments' => [$set, $escape],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Inserts an array of data as a batch into the database.
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to insert.
     * @param int $batch_size The number of rows to insert per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function insert_batch(string $table = '', array $set = [], int $batch_size = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // $set in insertBatch is expected to be an array of arrays
        // _set typically stores key/value pairs for a single row, so merging might be complex for batch.
        // Assuming $set overrides and only uses _set if $set is empty.
        if ($this->_set && empty($set)) {
            $set = [$this->_set];
        } elseif ($this->_set) {
            // Apply current _set to all rows in batch
            $set = array_map(fn ($row) => array_merge($this->_set, $row), $set);
        }

        // SQLite3 Auto-Increment batch handling (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $index_data */
            $index_data = $this->db->getIndexData($table);
            $primary = null;
            $auto_increment = null;

            // Set the default primary if the table has any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary = $val->fields[0];
                        $max_id = $this->db->table($table)->selectMax($primary)->get()->getRow($primary) ?? 0;
                        $auto_increment = (null !== $max_id ? $max_id + 1 : 1);

                        break;
                    }
                }
            }

            if (null !== $primary && null !== $auto_increment) {
                $new_set = [];

                foreach ($set as $val) {
                    // Ensure $val is treated as a row (assoc array)
                    if (! is_array($val)) {
                        continue;
                    }

                    // If it's a multi-dimensional array (e.g., from original's complex logic for inner loops, but simplified here)
                    // Assuming $set is array<int, array<string, mixed>> (array of rows)
                    if (! isset($val[$primary])) {
                        $val[$primary] = $auto_increment;
                        $auto_increment++;
                    }

                    $new_set[] = $val;
                }
                $set = $new_set;
            }
        }

        if (! $batch_size) {
            $batch_size = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'insertBatch',
            'arguments' => [$set, $escape, $batch_size],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Updates data in the database.
     *
     * @param string $table The table name.
     * @param array<string, mixed> $set An associative array of data to update.
     * @param array<string, mixed> $where An associative array of WHERE conditions.
     * @param int|null $limit The maximum number of rows to update.
     */
    public function update(string $table = '', array $set = [], array $where = [], ?int $limit = null)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit for non-Postgre/SQLite3 or SQLSRV version >= 10
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre', 'SQLite3']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $is_db_limit_compatible) {
            $this->_limit = $limit;
        }

        $set = array_merge($this->_set, $set);

        // Normalize where clause (original logic adapted)
        foreach ($where as $key => $val) {
            if (is_array($val) && isset($val['value'])) {
                $where[$key] = $val['value'];
            } else {
                $where[$key] = $val;
            }
        }

        $this->_prepare[] = [
            'function' => 'update',
            // Pass $this->_limit only if the database driver is not Postgre or SQLite3 (or if CI handles it for SQLSRV)
            'arguments' => [$set, $where, (! in_array($this->db->DBDriver, ['Postgre', 'SQLite3']) ? $this->_limit : null)],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Updates an array of data as a batch in the database.
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to update.
     * @param array<int, string>|string $constraint The column(s) to use for the WHERE clause (e.g., primary key).
     * @param int $batch_size The number of rows to update per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function update_batch(string $table = '', array $set = [], array|string $constraint = [], int $batch_size = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            // Apply current _set to all rows in batch
            $set = array_map(fn ($row) => array_merge($this->_set, $row), $set);
        }

        if (! $batch_size) {
            $batch_size = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'updateBatch',
            'arguments' => [$set, $constraint, $batch_size],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Update data or insert if record is not exists (UPSERT operation).
     *
     * @param string $table The table name.
     * @param array<string, mixed> $set An associative array of data to insert/update.
     * @param bool $escape Whether to escape the data.
     */
    public function upsert(string $table = '', array $set = [], bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            $set = array_merge($this->_set, $set);
        }

        // SQLite3 Auto-Increment handling for UPSERT (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $index_data */
            $index_data = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary_field = $val->fields[0];
                        // Only set if not already present in $set (which it shouldn't be for an INSERT part of upsert)
                        if (! isset($set[$primary_field])) {
                            $max_id = $this->db->table($table)->selectMax($primary_field)->get()->getRow($primary_field) ?? 0;
                            $set[$primary_field] = (null !== $max_id ? $max_id + 1 : 1);
                        }

                        break;
                    }
                }
            }
        }

        $this->_prepare[] = [
            'function' => 'upsert',
            'arguments' => [$set, $escape],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Batch update data or insert if record is not exists (UPSERT BATCH operation).
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to insert/update.
     * @param int $batch_size The number of rows to process per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function upsert_batch(string $table = '', array $set = [], int $batch_size = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if ($this->_set) {
            // Apply current _set to all rows in batch
            $set = array_map(fn ($row) => array_merge($this->_set, $row), $set);
        }

        // SQLite3 Auto-Increment batch handling for UPSERT (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $index_data */
            $index_data = $this->db->getIndexData($table);
            $primary = null;
            $auto_increment = null;

            // Set the default primary if the table has any primary column
            if ($index_data) {
                // Loops to get the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary = $val->fields[0];
                        $max_id = $this->db->table($table)->selectMax($primary)->get()->getRow($primary) ?? 0;
                        $auto_increment = (null !== $max_id ? $max_id + 1 : 1);

                        break;
                    }
                }
            }

            if (null !== $primary && null !== $auto_increment) {
                $new_set = [];

                foreach ($set as $val) {
                    // Ensure $val is treated as a row (assoc array)
                    if (! is_array($val)) {
                        continue;
                    }

                    // Apply auto-increment logic only if primary key is not set in the row (assuming insert part)
                    if (! isset($val[$primary])) {
                        $val[$primary] = $auto_increment;
                        $auto_increment++;
                    }

                    $new_set[] = $val;
                }
                $set = $new_set;
            }
        }

        if (! $batch_size) {
            $batch_size = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'upsertBatch',
            'arguments' => [$set, $escape, $batch_size],
        ];

        /** @var int */
        return $this->_run_query();
    }

    /**
     * Executes a REPLACE statement (delete and insert).
     *
     * @param string $table The table name.
     * @param array<string, mixed> $set An associative array of data to replace.
     */
    public function replace(string $table = '', array $set = [])
    {
        if ($this->_set) {
            $set = array_merge($this->_set, $set);
        }

        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'replace',
            'arguments' => [$set],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Executes a DELETE statement.
     *
     * @param string $table The table name.
     * @param array<string, mixed> $where An associative array of WHERE conditions.
     * @param int|null $limit The maximum number of rows to delete.
     * @param bool $reset_data Whether to reset the query data after deletion (defaults to true).
     */
    public function delete(string $table = '', array $where = [], ?int $limit = null, bool $reset_data = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit for non-Postgre or SQLSRV version >= 10
        $is_db_limit_compatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $is_db_limit_compatible) {
            $this->_limit = $limit;
        }

        $this->_prepare[] = [
            'function' => 'delete',
            // Pass $this->_limit only if the database driver is not Postgre
            'arguments' => [$where, (! in_array($this->db->DBDriver, ['Postgre']) ? $this->_limit : null)],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Executes a TRUNCATE statement to quickly empty a table.
     *
     * @param string $table The table name.
     */
    public function truncate(string $table = '')
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'truncate',
            'arguments' => [],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Executes an EMPTY TABLE statement (usually DELETE FROM table).
     *
     * @param string $table The table name.
     */
    public function empty_table(string $table = '')
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'emptyTable',
            'arguments' => [],
        ];

        /** @var ResultInterface */
        return $this->_run_query();
    }

    /**
     * Starts a manual database transaction.
     *
     * @return $this
     */
    public function trans_begin(): self
    {
        $this->db->transBegin();

        return $this;
    }

    /**
     * Starts a transaction, committing or rolling back automatically when complete.
     *
     * @return $this
     */
    public function trans_start(): self
    {
        $this->db->transStart();

        return $this;
    }

    /**
     * Completes an auto-managed transaction (started with trans_start()).
     */
    public function trans_complete(): bool
    {
        return $this->db->transComplete();
    }

    /**
     * Get the current transaction status.
     */
    public function trans_status(): bool
    {
        return $this->db->transStatus();
    }

    /**
     * Transaction Commit
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_commit(): bool
    {
        return $this->db->transCommit();
    }

    /**
     * Transaction Rolling Back
     * Your contribution is needed to write complete hint about this method
     */
    public function trans_rollback(): bool
    {
        return $this->db->transRollback();
    }

    /**
     * Your contribution is needed to write complete hint about this method
     *
     * @return array<string, mixed>
     */
    public function error(): array
    {
        return $this->db->error();
    }

    /**
     * Run the query of collected property.
     * Executes the prepared query chain on the builder and resets the state.
     */
    private function _run_query()
    {
        $executed = false;

        // 1. Initialize Builder if not set
        if (! $this->_builder) {
            if ($this->_is_query) {
                // For raw queries, use the connection itself
                $this->_builder = $this->db;
            } elseif ($this->_table) {
                // Use Query Builder for table-based operations
                /** @var BaseBuilder $builder_instance */
                $builder_instance = $this->db->table($this->_table);
                $this->_builder = $builder_instance;

                if ($this->_limit) {
                    // Apply limit/offset for query builder if set
                    $this->_builder->limit($this->_limit, $this->_offset ?? 0);
                }

                if (! $this->_selection) {
                    // Default select all if no explicit select was called
                    $this->_builder->select('*');
                }
            } else {
                // Builder initialization failed (e.g., table() was never called)
                $this->_reset_properties();
            }
        }

        $query = $this->_builder;

        // Builder methods that execute the query and return a Result or success/failure bool
        $execution_filters = [
            'get', 'getWhere', 'countAll', 'countAllResults',
            'insert', 'insertBatch', 'update', 'updateBatch',
            'upsert', 'upsertBatch', 'replace',
            'delete', 'truncate', 'emptyTable',
        ];

        // Methods that operate on the already executed Query Result (getResultObject, getRow, etc.)
        $result_methods_filter = [
            'getNumRows',
            'getResultObject', 'getResultArray',
            'getRow', 'getRowArray', 'getRowObject',
        ];

        // Track if we need to execute get() before result methods
        $has_result_method = false;
        $result_method_data = null;

        foreach ($this->_prepare as $val) {
            $function = $val['function'];
            $arguments = $val['arguments'];

            // Check if this is a result method
            if (in_array($function, $result_methods_filter)) {
                $has_result_method = true;
                $result_method_data = $val;
                continue; // Skip processing now, handle after
            }

            // Handle special functions (Subquery logic)
            if ('selectSubquery' === $function || 'fromSubquery' === $function) {
                if (isset($arguments[0]) && $arguments[0] instanceof self && $query instanceof BaseBuilder) {
                    $subquery_object = $arguments[0];
                    $subquery_builder = $this->_extract_builder($subquery_object);
                    $alias = (isset($arguments[1]) ? $arguments[1] : ('fromSubquery' == $function ? 'subquery' : ''));

                    if ($subquery_builder) {
                        $query->$function($subquery_builder, $alias);
                    }
                }
                $executed = true;
                continue;
            }

            // Check if the method exists on the current query object/builder before calling
            if (! method_exists($query, $function)) {
                continue;
            }

            // Call the method dynamically with all arguments
            $query = call_user_func_array([$query, $function], $arguments);

            // Set flags for execution/result processing
            if (in_array($function, $execution_filters)) {
                $executed = true;
                if ('get' === $function || 'getWhere' === $function) {
                    $this->_get = true;
                }
            } else {
                // Regular chaining method (select, where, join, etc.)
                $executed = true;
            }
        }

        // Now handle result method if one was queued
        if ($has_result_method && $result_method_data) {
            $function = $result_method_data['function'];
            $arguments = $result_method_data['arguments'];

            // If we haven't executed get() yet, do it now
            if (! $this->_get && $query instanceof BaseBuilder) {
                $query = $query->get();
                $this->_get = true;
            }

            // Now call the result method on the result object
            if (method_exists($query, $function)) {
                $query = call_user_func_array([$query, $function], $arguments);
            }

            $executed = true;
            $this->_finished = true;
        }

        // Final result handling
        if ($executed) {
            $this->_reset_properties();

            // Return the result
            return $query;
        }

        return null;
    }

    /**
     * Resets internal properties after query execution.
     */
    private function _reset_properties(): void
    {
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
        $this->_selection = false;
    }

    /**
     * Casting the column for PostgreSQL and SQLSRV to handle type-specific queries.
     *
     * @param string|null $column The column name, potentially including an operator.
     * @param mixed $value The value for comparison.
     * @return array{column: string, value: mixed, escape: bool}
     */
    private function _cast_column(?string $column = null, mixed $value = ''): array
    {
        $column = trim((string) $column);
        $operand = null;
        $escape = true;

        if (strpos($column, ' ') !== false) {
            // Get operand if any
            $parts = explode(' ', $column, 2);
            $base_column = $parts[0];
            $get_operand = strtoupper(trim($parts[1]));

            if (in_array($get_operand, ['!=', '>=', '<=', '>', '<', '='])) { // Added '=' for completeness
                // Remove operand from column
                    $column = $base_column;

                // Set operand
                $operand = $get_operand;
            } elseif (in_array($get_operand, ['IS NULL', 'IS NOT NULL'])) {
                // Remove operand from column
                $column = $base_column;

                // Set operand
                $operand = $get_operand;

                // Set escape
                $escape = false;
            }
        }

        if (in_array($this->db->DBDriver, ['SQLSRV', 'Postgre']) && ! stripos($column, '(') && ! stripos($column, ')')) {
            $cast_type = 'VARCHAR'; // Default cast type

            // Determine data type and cast type based on value
            if (is_int($value)) {
                $cast_type = 'INTEGER';
                $value = (int) $value;
            } elseif (is_float($value)) {
                // CodeIgniter treats 'double' as float, matching CI's type mapping
                $cast_type = 'DOUBLE'; // Or 'FLOAT'
                $value = (float) $value;
            } elseif (is_string($value) && (\DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false)) {
                $cast_type = ('SQLSRV' == $this->db->DBDriver ? 'DATETIME' : 'TIMESTAMP');
            } elseif (is_string($value) && (\DateTime::createFromFormat('Y-m-d', $value) !== false)) {
                $cast_type = 'DATE';
            } elseif (! is_array($value) && null !== $value) {
                $cast_type = 'VARCHAR' . ('SQLSRV' == $this->db->DBDriver ? '(MAX)' : '');
                $value = (string) $value;
            }

            $column_name_only = (stripos($column, ' ') !== false ? substr($column, 0, stripos($column, ' ')) : $column);

            if ('SQLSRV' == $this->db->DBDriver) {
                $column = 'CONVERT(' . $cast_type . ', ' . $column_name_only . ')';
            } else {
                $column = 'CAST(' . $column_name_only . ' AS ' . $cast_type . ')';
            }

            if (strpos($cast_type, 'VARCHAR') !== false) {
                $column = 'LOWER(' . $column . ')';
                if (is_string($value)) {
                    $value = strtolower($value);
                }
            }
        }

        return [
            'column' => $column . ($operand ? ' ' . $operand : ''),
            'value' => $value,
            'escape' => $escape,
        ];
    }

    /**
     * Extract builder from subquery object.
     * Extracts CodeIgniter Query Builder from the custom Model subquery object.
     *
     * @param self $subquery_object The subquery object instance.
     */
    private function _extract_builder(self $subquery_object): ?BaseBuilder
    {
        if (! ($subquery_object instanceof self)) {
            return null;
        }

        if ($subquery_object->_table) {
            // FORCE REBUILD builder for subquery
            /** @var BaseBuilder $builder_instance */
            $builder_instance = $this->db->table($subquery_object->_table);
            $subquery_object->_builder = $builder_instance;

            // Process all prepare statements for the subquery
            foreach ($subquery_object->_prepare as $prepare) {
                $function = $prepare['function'];
                $arguments = $prepare['arguments'];

                if (! method_exists($subquery_object->_builder, $function)) {
                    continue;
                }

                // Recursively call _extract_builder for nested subqueries
                if (('selectSubquery' === $function || 'fromSubquery' === $function) && isset($arguments[0]) && $arguments[0] instanceof self) {
                    $nested_builder = $this->_extract_builder($arguments[0]);
                    $alias = $arguments[1] ?? '';
                    if ($nested_builder) {
                        call_user_func_array([$subquery_object->_builder, $function], [$nested_builder, $alias]);
                    }
                } else {
                    call_user_func_array([$subquery_object->_builder, $function], $arguments);
                }
            }

            if (! $subquery_object->_selection) {
                $subquery_object->_builder->select('*');
            }
        }

        /** @var BaseBuilder|null */
        return $subquery_object->_builder;
    }
}
