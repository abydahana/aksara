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
use DateTime;
use Throwable;

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
    private bool $_isQuery = false;

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
    public function databaseConfig(string|int|array|null $driver = null, ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null): self|false
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
            } catch (Throwable $e) {
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
            } catch (Throwable $e) {
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
            } catch (Throwable $e) {
                return function_exists('throw_exception') ? throw_exception(403, $e->getMessage()) : false;
            }
        }

        return $this;
    }

    /**
     * Get the database driver.
     */
    public function dbDriver(): string
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
    public function disableForeignKey(): void
    {
        $this->db->disableForeignKeyChecks();
    }

    /**
     * Enable foreign key check for truncating the table.
     */
    public function enableForeignKey(): void
    {
        $this->db->enableForeignKeyChecks();
    }

    /**
     * List available tables on current active database.
     *
     * @return array<int, string>
     */
    public function listTables(): array
    {
        return $this->db->listTables();
    }

    /**
     * Check the existence of a table on the current active database.
     *
     * @param string $table The table name.
     */
    public function tableExists(string $table): bool
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
    public function fieldExists(string $field, string $table): bool
    {
        if (strpos(trim($table), '(') !== false || strpos(strtolower(trim($table)), 'select ') !== false) {
            return false;
        }

        // Store alias for later use (though $_tableAlias is not defined in properties, following original logic)
        $tempTableAlias = [];
        if (strpos(trim($table), ' ') !== false) {
            $table = str_ireplace(' AS ', ' ', $table);
            $destructure = explode(' ', $table);
            $table = $destructure[0];

            $tempTableAlias[$destructure[1]] = $table; // This variable is local now
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
    public function listFields(string $table): array|false
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
    public function fieldData(string $table): array|false
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
    public function indexData(string $table): array|false
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
    public function foreignData(string $table): array|false
    {
        if ($table && $this->db->tableExists($table)) {
            return $this->db->getForeignKeyData($table);
        }

        return false;
    }

    /**
     * Get the number of affected rows by the last query.
     */
    public function affectedRows(): int
    {
        return $this->db->affectedRows();
    }

    /**
     * Get the ID generated by the last insert statement.
     */
    public function insertId(): int|string
    {
        return $this->db->insertID();
    }

    /**
     * Getting the last executed query.
     */
    public function lastQuery(): Query|string
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
        $extractTable = preg_replace('/\(([^()]*+|(?R))*\)/', '', $query);

        // Get primary table
        preg_match('/FROM[\s]+(.*?)[\s]+/i', $extractTable, $matches);

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

        $this->_isQuery = true;

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
    public function selectCount(string $column, ?string $alias = null): self
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
    public function selectSum(string $column, ?string $alias = null): self
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
    public function selectMin(string $column, ?string $alias = null): self
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
    public function selectMax(string $column, ?string $alias = null): self
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
    public function selectAvg(string $column, ?string $alias = null): self
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
    public function selectSubquery(self $subquery, string $alias): self
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
    public function fromSubquery(self $subquery, string $alias): self
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
    public function newQuery(): self
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
                $cast = $this->_castColumn($key, $val, $escape);

                $this->_prepare[] = [
                    'function' => 'where',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($field, $value, $escape);

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
    public function orWhere(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run or where command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_castColumn($key, $val, $escape);

                $this->_prepare[] = [
                    'function' => 'orWhere',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($field, $value, $escape);

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
    public function whereIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function orWhereIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function whereNotIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function orWhereNotIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function like(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function orLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function notLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function orNotLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
                $cast = $this->_castColumn($key, $val, $escape);

                $this->_prepare[] = [
                    'function' => 'having',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($field, $value, $escape);

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
    public function orHaving(string|array $field = '', mixed $value = '', bool $escape = true): self
    {
        if (is_array($field)) {
            // Run or having command
            foreach ($field as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_castColumn($key, $val, $escape);

                $this->_prepare[] = [
                    'function' => 'orHaving',
                    'arguments' => [$cast['column'], $cast['value'], $cast['escape']],
                ];
            }
        } else {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($field, $value, $escape);

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
    public function havingIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function orHavingIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function havingNotIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
    public function orHavingNotIn(string|array $field = '', ?array $value = null, bool $escape = true): self
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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function havingLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function orHavingLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function notHavingLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
     * @param bool $caseInsensitive Whether the search is case-insensitive.
     * @return $this
     */
    public function orNotHavingLike(string|array $field = '', mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): self
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
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        } else {
            foreach ($field as $key => $val) {
                $column[$key] = [
                    'match' => ($val ? $val : ''),
                    'side' => 'both',
                    'escape' => $escape,
                    'case_insensitive' => $caseInsensitive,
                ];
            }
        }

        foreach ($column as $key => $val) {
            /** @var array<string, mixed> $cast */
            $cast = $this->_castColumn($key, $val['match'], $escape);

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
    public function groupBy(string|array|null $column = null): self
    {
        if (in_array($this->db->DBDriver, ['SQLSRV'], true)) {
            $columnsArray = is_string($column) ? array_map('trim', explode(',', $column)) : ($column ?? []);

            // Loops the group list
            foreach ($columnsArray as $val) {
                if (stripos($val, '(') !== false && stripos($val, ')') !== false) {
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        'arguments' => [$val],
                    ];
                } else {
                    $columnName = (stripos($val, ' AS ') !== false ? substr($val, 0, stripos($val, ' AS ')) : $val);
                    $this->_prepare[] = [
                        'function' => 'groupBy',
                        // CodeIgniter 4 may handle this differently, adapting to original's intention for SQLSRV
                        'arguments' => ['CONVERT(VARCHAR(MAX), ' . $columnName . ')'],
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
    public function orderBy(string|array|null $column = null, string $direction = '', bool $escape = true): self
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
            $columnsArray = ($column ? array_map('trim', preg_split('/,(?![^(]+\))/', trim($column))) : []);

            foreach ($columnsArray as $val) {
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
    public function limit(?int $limit = null, ?int $offset = null): self
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
    public function groupStart(): self
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
    public function orGroupStart(): self
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
    public function notGroupStart(): self
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
    public function orNotGroupStart(): self
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
    public function groupEnd(): self
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
    public function havingGroupStart(): self
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
    public function orHavingGroupStart(): self
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
    public function notHavingGroupStart(): self
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
    public function orNotHavingGroupStart(): self
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
    public function havingGroupEnd(): self
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
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $isDbLimitCompatible) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        $this->_prepare[] = [
            // Only pass $limit and $offset if we rely on CI's get() to handle it, otherwise internal _runQuery will handle limit/offset for pre-CI versions
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
    public function getWhere(string $table = '', array $where = [], ?int $limit = null, ?int $offset = null, bool $reset = true): self
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit/offset for non-SQLSRV/Postgre or SQLSRV version >= 10
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $isDbLimitCompatible) {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        if ($where && 'Postgre' == $this->db->DBDriver) {
            foreach ($where as $key => $val) {
                /** @var array<string, mixed> $cast */
                $cast = $this->_castColumn($key, $val);

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
    public function resetQuery(): self
    {
        $this->_runQuery();

        // The _runQuery will reset properties internally if it was marked as finished
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
        return $this->_runQuery();
    }

    /**
     * Executes the query and returns the results as an array of arrays.
     *
     * @return array<int, array<string, mixed>>|ResultInterface
     */
    public function resultArray()
    {
        $this->_prepare[] = [
            'function' => 'getResultArray',
            'arguments' => [],
        ];

        /** @var array<int, array<string, mixed>>|ResultInterface */
        return $this->_runQuery();
    }

    /**
     * Executes the query and returns a single row as an object.
     *
     * @param int|string $field The row number to retrieve, or the field name to return directly.
     */
    public function row(int|string $field = 0)
    {
        // Apply limit for non-SQLSRV/Postgre or SQLSRV version >= 10 when retrieving a single row object.
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($isDbLimitCompatible) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => ($field && is_int($field) ? 'getRowObject' : 'getRow'),
            'arguments' => [$field],
        ];

        /** @var object|string|int|float|bool|null */
        return $this->_runQuery();
    }

    /**
     * Executes the query and returns a single row as an array.
     *
     * @param int|string $field The row number to retrieve, or the field name to return directly.
     * @return array<string, mixed>|string|int|float|bool|null
     */
    public function rowArray(int|string $field = 1)
    {
        // Apply limit for non-SQLSRV/Postgre or SQLSRV version >= 10 when retrieving a single row array.
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($isDbLimitCompatible) {
            $this->_limit = 1;
        }

        $this->_prepare[] = [
            'function' => 'getRowArray',
            'arguments' => [$field],
        ];

        return $this->_runQuery();
    }

    /**
     * Get the number of rows from a query.
     *
     * @param string $table The table name (optional, only used if not set via `from()`/`table()`).
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function numRows(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'getNumRows',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_runQuery();
    }

    /**
     * Counts all rows in the specified table.
     *
     * @param string $table The table name.
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function countAll(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAll',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_runQuery();
    }

    /**
     * Counts the rows of the last executed query result, respecting WHERE and other clauses.
     *
     * @param string $table The table name (optional, only used if not set via `from()`/`table()`).
     * @param bool $reset Whether to reset the query parameters after execution.
     */
    public function countAllResults(string $table = '', bool $reset = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'countAllResults',
            'arguments' => [$reset],
        ];

        /** @var int */
        return $this->_runQuery();
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
            }
        } else {
            $this->_prepare[] = [
                'function' => 'set',
                'arguments' => [$column, $value, $escape],
            ];
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

        // SQLite3 Auto-Increment handling (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $indexData */
            $indexData = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($indexData) {
                // Loops to get the primary key
                foreach ($indexData as $key => $val) {
                    // Check if the field has primary key
                    // Assuming $val is an object with 'type' and 'fields' properties
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primaryField = $val->fields[0];
                        // Get max ID and increment
                        $maxId = $this->db->table($table)->selectMax($primaryField)->get()->getRow($primaryField) ?? 0;
                        $set[$primaryField] = (null !== $maxId ? $maxId + 1 : 1);

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
        return $this->_runQuery();
    }

    /**
     * Inserts an array of data as a batch into the database.
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to insert.
     * @param int $batchSize The number of rows to insert per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function insertBatch(string $table = '', array $set = [], int $batchSize = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // SQLite3 Auto-Increment batch handling (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $indexData */
            $indexData = $this->db->getIndexData($table);
            $primary = null;
            $autoIncrement = null;

            // Set the default primary if the table has any primary column
            if ($indexData) {
                // Loops to get the primary key
                foreach ($indexData as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary = $val->fields[0];
                        $maxId = $this->db->table($table)->selectMax($primary)->get()->getRow($primary) ?? 0;
                        $autoIncrement = (null !== $maxId ? $maxId + 1 : 1);

                        break;
                    }
                }
            }

            if (null !== $primary && null !== $autoIncrement) {
                $newSet = [];

                foreach ($set as $val) {
                    // Ensure $val is treated as a row (assoc array)
                    if (! is_array($val)) {
                        continue;
                    }

                    // If it's a multi-dimensional array (e.g., from original's complex logic for inner loops, but simplified here)
                    // Assuming $set is array<int, array<string, mixed>> (array of rows)
                    if (! isset($val[$primary])) {
                        $val[$primary] = $autoIncrement;
                        $autoIncrement++;
                    }

                    $newSet[] = $val;
                }
                $set = $newSet;
            }
        }

        if (! $batchSize) {
            $batchSize = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'insertBatch',
            'arguments' => [$set, $escape, $batchSize],
        ];

        /** @var int */
        return $this->_runQuery();
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
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre', 'SQLite3'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $isDbLimitCompatible) {
            $this->_limit = $limit;
        }

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
            'arguments' => [$set, $where, (! in_array($this->db->DBDriver, ['Postgre', 'SQLite3'], true) ? $this->_limit : null)],
        ];

        /** @var ResultInterface */
        return $this->_runQuery();
    }

    /**
     * Updates an array of data as a batch in the database.
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to update.
     * @param array<int, string>|string $constraint The column(s) to use for the WHERE clause (e.g., primary key).
     * @param int $batchSize The number of rows to update per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function updateBatch(string $table = '', array $set = [], array|string $constraint = [], int $batchSize = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        if (! $batchSize) {
            $batchSize = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'updateBatch',
            'arguments' => [$set, $constraint, $batchSize],
        ];

        /** @var int */
        return $this->_runQuery();
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

        // SQLite3 Auto-Increment handling for UPSERT (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $indexData */
            $indexData = $this->db->getIndexData($table);

            // Set the default primary if the table have any primary column
            if ($indexData) {
                // Loops to get the primary key
                foreach ($indexData as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primaryField = $val->fields[0];
                        // Only set if not already present in $set (which it shouldn't be for an INSERT part of upsert)
                        if (! isset($set[$primaryField])) {
                            $maxId = $this->db->table($table)->selectMax($primaryField)->get()->getRow($primaryField) ?? 0;
                            $set[$primaryField] = (null !== $maxId ? $maxId + 1 : 1);
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
        return $this->_runQuery();
    }

    /**
     * Batch update data or insert if record is not exists (UPSERT BATCH operation).
     *
     * @param string $table The table name.
     * @param array<int, array<string, mixed>> $set An array of associative arrays of data to insert/update.
     * @param int $batchSize The number of rows to process per batch. 0 for all at once.
     * @param bool $escape Whether to escape the data.
     * @return int The number of affected rows.
     */
    public function upsertBatch(string $table = '', array $set = [], int $batchSize = 0, bool $escape = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // SQLite3 Auto-Increment batch handling for UPSERT (original logic adapted)
        if ('SQLite3' == $this->db->DBDriver && $table && $this->db->tableExists($table)) {
            /** @var array<string, \CodeIgniter\Database\IndexData>|false $indexData */
            $indexData = $this->db->getIndexData($table);
            $primary = null;
            $autoIncrement = null;

            // Set the default primary if the table has any primary column
            if ($indexData) {
                // Loops to get the primary key
                foreach ($indexData as $key => $val) {
                    // Check if the field has primary key
                    if (isset($val->type) && 'PRIMARY' == $val->type && isset($val->fields[0])) {
                        $primary = $val->fields[0];
                        $maxId = $this->db->table($table)->selectMax($primary)->get()->getRow($primary) ?? 0;
                        $autoIncrement = (null !== $maxId ? $maxId + 1 : 1);

                        break;
                    }
                }
            }

            if (null !== $primary && null !== $autoIncrement) {
                $newSet = [];

                foreach ($set as $val) {
                    // Ensure $val is treated as a row (assoc array)
                    if (! is_array($val)) {
                        continue;
                    }

                    // Apply auto-increment logic only if primary key is not set in the row (assuming insert part)
                    if (! isset($val[$primary])) {
                        $val[$primary] = $autoIncrement;
                        $autoIncrement++;
                    }

                    $newSet[] = $val;
                }
                $set = $newSet;
            }
        }

        if (! $batchSize) {
            $batchSize = sizeof($set);
        }

        $this->_prepare[] = [
            'function' => 'upsertBatch',
            'arguments' => [$set, $escape, $batchSize],
        ];

        /** @var int */
        return $this->_runQuery();
    }

    /**
     * Executes a REPLACE statement (delete and insert).
     *
     * @param string $table The table name.
     * @param array<string, mixed> $set An associative array of data to replace.
     */
    public function replace(string $table = '', array $set = [])
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'replace',
            'arguments' => [$set],
        ];

        /** @var ResultInterface */
        return $this->_runQuery();
    }

    /**
     * Executes a DELETE statement.
     *
     * @param string $table The table name.
     * @param array<string, mixed> $where An associative array of WHERE conditions.
     * @param int|null $limit The maximum number of rows to delete.
     * @param bool $resetData Whether to reset the query data after deletion (defaults to true).
     */
    public function delete(string $table = '', array $where = [], ?int $limit = null, bool $resetData = true)
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        // Apply limit for non-Postgre or SQLSRV version >= 10
        $isDbLimitCompatible = ! in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) || ('SQLSRV' === $this->db->DBDriver && (method_exists($this->db, 'getVersion') && $this->db->getVersion() >= 10));

        if ($limit && $isDbLimitCompatible) {
            $this->_limit = $limit;
        }

        $this->_prepare[] = [
            'function' => 'delete',
            // Pass $this->_limit only if the database driver is not Postgre
            'arguments' => [$where, (! in_array($this->db->DBDriver, ['Postgre'], true) ? $this->_limit : null)],
        ];

        /** @var ResultInterface */
        return $this->_runQuery();
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
        return $this->_runQuery();
    }

    /**
     * Executes an EMPTY TABLE statement (usually DELETE FROM table).
     *
     * @param string $table The table name.
     */
    public function emptyTable(string $table = '')
    {
        if (! $this->_table && $table) {
            $this->_table = $table;
        }

        $this->_prepare[] = [
            'function' => 'emptyTable',
            'arguments' => [],
        ];

        /** @var ResultInterface */
        return $this->_runQuery();
    }

    /**
     * Compiles a SELECT query string and returns the SQL.
     *
     * @param   bool $reset Whether to reset the query builder values.
     * @return  string
     */
    public function getCompiledSelect($reset = true)
    {
        $this->_prepare[] = [
            'function' => 'getCompiledSelect',
            'arguments' => [$reset]
        ];

        return $this->_runQuery();
    }

    /**
     * Compiles an INSERT query string and returns the SQL.
     *
     * @param   bool $reset Whether to reset the query builder values.
     * @return  string
     */
    public function getCompiledInsert($reset = true)
    {
        $this->_prepare[] = [
            'function' => 'getCompiledInsert',
            'arguments' => [$reset]
        ];

        return $this->_runQuery();
    }

    /**
     * Compiles an UPDATE query string and returns the SQL.
     *
     * @param   bool $reset Whether to reset the query builder values.
     * @return  string
     */
    public function getCompiledUpdate($reset = true)
    {
        $this->_prepare[] = [
            'function' => 'getCompiledUpdate',
            'arguments' => [$reset]
        ];

        return $this->_runQuery();
    }

    /**
     * Compiles a DELETE query string and returns the SQL.
     *
     * @param   bool $reset Whether to reset the query builder values.
     * @return  string
     */
    public function getCompiledDelete($reset = true)
    {
        $this->_prepare[] = [
            'function' => 'getCompiledDelete',
            'arguments' => [$reset]
        ];

        return $this->_runQuery();
    }

    /**
     * Starts a manual database transaction.
     *
     * @return $this
     */
    public function transBegin(): self
    {
        $this->db->transBegin();

        return $this;
    }

    /**
     * Starts a transaction, committing or rolling back automatically when complete.
     *
     * @return $this
     */
    public function transStart(): self
    {
        $this->db->transStart();

        return $this;
    }

    /**
     * Completes an auto-managed transaction (started with trans_start()).
     */
    public function transComplete(): bool
    {
        return $this->db->transComplete();
    }

    /**
     * Get the current transaction status.
     */
    public function transStatus(): bool
    {
        return $this->db->transStatus();
    }

    /**
     * Transaction Commit
     * Your contribution is needed to write complete hint about this method
     */
    public function transCommit(): bool
    {
        return $this->db->transCommit();
    }

    /**
     * Transaction Rolling Back
     * Your contribution is needed to write complete hint about this method
     */
    public function transRollback(): bool
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
    private function _runQuery()
    {
        $executed = false;

        // 1. Initialize Builder if not set
        if (! $this->_builder) {
            if ($this->_isQuery) {
                // For raw queries, use the connection itself
                $this->_builder = $this->db;
            } elseif ($this->_table) {
                // Use Query Builder for table-based operations
                /** @var BaseBuilder $builderInstance */
                $builderInstance = $this->db->table($this->_table);
                $this->_builder = $builderInstance;

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
                $this->_resetProperties();
            }
        }

        $query = $this->_builder;

        // Builder methods that execute the query and return a Result or success/failure bool
        $executionFilters = [
            'get', 'getWhere', 'countAll', 'countAllResults',
            'insert', 'insertBatch', 'update', 'updateBatch',
            'upsert', 'upsertBatch', 'replace',
            'delete', 'truncate', 'emptyTable',
        ];

        // Methods that operate on the already executed Query Result (getResultObject, getRow, etc.)
        $resultMethodsFilter = [
            'getNumRows',
            'getResultObject', 'getResultArray',
            'getRow', 'getRowArray', 'getRowObject',
        ];

        // Track if we need to execute get() before result methods
        $hasResultMethod = false;
        $resultMethodData = null;

        foreach ($this->_prepare as $val) {
            $function = $val['function'];
            $arguments = $val['arguments'];

            // Check if this is a result method
            if (in_array($function, $resultMethodsFilter, true)) {
                $hasResultMethod = true;
                $resultMethodData = $val;
                continue; // Skip processing now, handle after
            }

            // Handle special functions (Subquery logic)
            if ('selectSubquery' === $function || 'fromSubquery' === $function) {
                if (isset($arguments[0]) && $arguments[0] instanceof self && $query instanceof BaseBuilder) {
                    $subqueryObject = $arguments[0];
                    $subqueryBuilder = $this->_extractBuilder($subqueryObject);
                    $alias = (isset($arguments[1]) ? $arguments[1] : ('fromSubquery' == $function ? 'subquery' : ''));

                    if ($subqueryBuilder) {
                        $query->$function($subqueryBuilder, $alias);
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
            if (in_array($function, $executionFilters, true)) {
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
        if ($hasResultMethod && $resultMethodData) {
            $function = $resultMethodData['function'];
            $arguments = $resultMethodData['arguments'];

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
            $this->_resetProperties();

            // Return the result
            return $query;
        }

        return null;
    }

    /**
     * Resets internal properties after query execution.
     */
    private function _resetProperties(): void
    {
        $this->_builder = null;
        $this->_prepare = [];
        $this->_finished = false;
        $this->_ordered = false;
        $this->_from = null;
        $this->_table = null;
        $this->_limit = null;
        $this->_offset = null;
        $this->_get = false;
        $this->_isQuery = false;
        $this->_selection = false;
    }

    /**
     * Casting the column for PostgreSQL and SQLSRV to handle type-specific queries.
     *
     * @param string|null $column The column name, potentially including an operator.
     * @param mixed $value The value for comparison.
     * @return array{column: string, value: mixed, escape: bool}
     */
    private function _castColumn(?string $column = null, mixed $value = '', bool $escape = true): array
    {
        $column = trim((string) $column);
        $operand = null;

        if (strpos($column, ' ') !== false) {
            // Get operand if any
            $parts = explode(' ', $column, 2);
            $baseColumn = $parts[0];
            $getOperand = strtoupper(trim($parts[1]));

            if (in_array($getOperand, ['!=', '>=', '<=', '>', '<', '='], true)) { // Added '=' for completeness
                // Remove operand from column
                    $column = $baseColumn;

                // Set operand
                $operand = $getOperand;
            } elseif (in_array(strtoupper($getOperand), ['IS NULL', 'IS NOT NULL'], true)) {
                // Remove operand from column
                $column = $baseColumn;

                // Set operand
                $operand = $getOperand;

                // Set escape
                $escape = false;
            }
        }

        if (in_array($this->db->DBDriver, ['SQLSRV', 'Postgre'], true) && ! stripos($column, '(') && ! stripos($column, ')')) {
            $castType = 'VARCHAR'; // Default cast type

            // Determine data type and cast type based on value
            if (is_int($value)) {
                $castType = 'INTEGER';
                $value = (int) $value;
            } elseif (is_float($value)) {
                // CodeIgniter treats 'double' as float, matching CI's type mapping
                $castType = 'DOUBLE'; // Or 'FLOAT'
                $value = (float) $value;
            } elseif (is_string($value) && (DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false)) {
                $castType = ('SQLSRV' == $this->db->DBDriver ? 'DATETIME' : 'TIMESTAMP');
            } elseif (is_string($value) && (DateTime::createFromFormat('Y-m-d', $value) !== false)) {
                $castType = 'DATE';
            } elseif (! is_array($value) && null !== $value) {
                $castType = 'VARCHAR' . ('SQLSRV' == $this->db->DBDriver ? '(MAX)' : '');
                $value = (string) $value;
            }

            $columnNameOnly = (stripos($column, ' ') !== false ? substr($column, 0, stripos($column, ' ')) : $column);

            if ('SQLSRV' == $this->db->DBDriver) {
                $column = 'CONVERT(' . $castType . ', ' . $columnNameOnly . ')';
            } else {
                $column = 'CAST(' . $columnNameOnly . ' AS ' . $castType . ')';
            }

            if (strpos($castType, 'VARCHAR') !== false) {
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
     * @param self $subqueryObject The subquery object instance.
     */
    private function _extractBuilder(self $subqueryObject): ?BaseBuilder
    {
        if (! ($subqueryObject instanceof self)) {
            return null;
        }

        if ($subqueryObject->_table) {
            // FORCE REBUILD builder for subquery
            /** @var BaseBuilder $builderInstance */
            $builderInstance = $this->db->table($subqueryObject->_table);
            $subqueryObject->_builder = $builderInstance;

            // Process all prepare statements for the subquery
            foreach ($subqueryObject->_prepare as $prepare) {
                $function = $prepare['function'];
                $arguments = $prepare['arguments'];

                if (! method_exists($subqueryObject->_builder, $function)) {
                    continue;
                }

                // Recursively call _extract_builder for nested subqueries
                if (('selectSubquery' === $function || 'fromSubquery' === $function) && isset($arguments[0]) && $arguments[0] instanceof self) {
                    $nestedBuilder = $this->_extractBuilder($arguments[0]);
                    $alias = $arguments[1] ?? '';
                    if ($nestedBuilder) {
                        call_user_func_array([$subqueryObject->_builder, $function], [$nestedBuilder, $alias]);
                    }
                } else {
                    call_user_func_array([$subqueryObject->_builder, $function], $arguments);
                }
            }

            if (! $subqueryObject->_selection) {
                $subqueryObject->_builder->select('*');
            }
        }

        /** @var BaseBuilder|null */
        return $subqueryObject->_builder;
    }
}
