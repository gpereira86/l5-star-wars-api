<?php

namespace system\core;

use DateTime;
use Exception;
use system\core\DbConection;
use system\model\UserModel;

/**
 * Abstract class responsible for logging data into a database.
 *
 * The `LogDbModel` class provides methods to log data into a specified database table. It ensures that the log data is validated,
 * filtered, and inserted properly. It also supports querying logs based on specific criteria and sorting.
 *
 * Features:
 * - Validates and filters log data via the `dataFilter` method before insertion.
 * - Supports sorting, limiting, and offsetting the result set.
 * - Provides the ability to fetch log records based on a date range.
 * - Supports API key validation for security.
 *
 * This class interacts with the database using the `DbConection` class for establishing connections and executing queries.
 */
abstract class LogDbModel
{
    protected $table;      // Name of the table where logs are stored.
    protected $params;     // Parameters for query execution.
    protected $sort;       // Sorting order for the query.
    protected $limit;      // Limit on the number of records returned.
    protected $offset;     // Offset for pagination.
    protected $query;      // SQL query being built or executed.

    /**
     * Constructor method for `LogDbModel` class.
     *
     * Initializes the class with the table name where the logs will be stored.
     *
     * @param string $table The name of the table in the database where logs will be saved.
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Filters the data set before inserting it into the database.
     *
     * This method ensures that each value in the dataset is properly sanitized using `filter_var`.
     * If the value is null, it remains null.
     *
     * @param array $dataSet The data to be filtered.
     * @return array The filtered dataset with sanitized values.
     */
    private function dataFilter(array $dataSet)
    {
        $filter = [];

        foreach ($dataSet as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    /**
     * Adds sorting to the query.
     *
     * Appends an `ORDER BY` clause to the query with the specified sorting order.
     *
     * @param string $order The column and order by which to sort the results (e.g., 'name ASC').
     * @return $this The current instance to allow method chaining.
     */
    public function sort(string $order)
    {
        $this->sort = " ORDER BY {$order}";
        return $this;
    }

    /**
     * Adds a limit to the query.
     *
     * Restricts the number of records returned by the query.
     *
     * @param string $limit The maximum number of records to return.
     * @return $this The current instance to allow method chaining.
     */
    public function limit(string $limit)
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * Adds an offset to the query.
     *
     * Skips the first X records based on the specified offset.
     *
     * @param string $offset The number of records to skip.
     * @return $this The current instance to allow method chaining.
     */
    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Constructs a search query for logs.
     *
     * This method creates a `SELECT` query to fetch log records, optionally with search terms, parameters, and column selection.
     *
     * @param string|null $terms The WHERE clause conditions for filtering results.
     * @param string|null $params The parameters to bind to the query.
     * @param string $columns The columns to select (default is '*').
     * @return $this The current instance to allow method chaining.
     */
    public function search(?string $terms = null, ?string $params = null, string $columns = '*')
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM " . $this->table . " WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM " . $this->table;
        return $this;
    }

    /**
     * Executes the search query and returns the results.
     *
     * This method executes the query built by the `search` method and applies any sorting, limiting, and offsetting.
     *
     * @param bool $all Whether to fetch all results or just the first result.
     * @return array|null An array of results if `$all` is true, a single object if false, or null if no results.
     */
    public function result(bool $all = false)
    {
        try {
            $stmt = DbConection::getInstance()->prepare($this->query . $this->sort . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);

        } catch (\PDOException $ex) {
            return 'Error => code: '.$ex->getCode().' | description: '.$ex->getMessage();
        }
    }

    /**
     * Registers log data into the database.
     *
     * This method constructs an `INSERT INTO` SQL query to save log data into the specified table.
     *
     * @param array $data The log data to be saved.
     * @return int|null The last inserted ID on success, or null on failure.
     */
    protected function register(array $data)
    {
        try {
            $columns = implode(',', array_keys($data));
            $values = ':' . implode(',:', array_keys($data));

            $this->query = "INSERT INTO " . $this->table . " ({$columns}) VALUES ({$values})";

            $stmt = DbConection::getInstance()->prepare($this->query);
            $stmt->execute($this->dataFilter($data));

            return DbConection::getInstance()->lastInsertId();
        } catch (\PDOException $ex) {
            return 'Error => code: '.$ex->getCode().' | description: '.$ex->getMessage();
        }
    }

    /**
     * Saves the log data by calling the `register` method.
     *
     * This is a wrapper for the `register` method, providing a simplified interface to save log data.
     *
     * @param array $data The log data to be saved.
     * @return int|null The last inserted ID on success, or null on failure.
     */
    public function save(array $data)
    {
        return $this->register($data);
    }

    /**
     * Retrieves log records based on a specified date range.
     *
     * This method allows fetching logs within a dynamic range (e.g., the last 7, 15, or 30 days).
     * The date range can be adjusted using the `$days` parameter. Optionally, the `finished` parameter allows setting the end date for the range.
     *
     * @param string|null $days The number of days to search back (7, 15, 30, or a custom range).
     * @param string|null $finished The end date for the search (in 'Y-m-d H:i:s' format).
     * @param string $apiKey The API key used to authenticate the request.
     * @return array The response containing log data or an error message.
     */
    public function getLogRegister(string $days = null, string $finished = null, string $apiKey)
    {
        try {
            if ($days == '7' || $days == '15' || $days == '30' || empty($days)) {

                $daysSearch = empty($days) ? 4 : $days;

                $date = new DateTime($finished);
                $dayFinished = is_null($finished) ? $date->format('Y-m-d H:i:s') : $finished;

                $date->setTime(0, 0);
                $date->modify("-".$daysSearch." days");

                $formattedDate = $date->format('Y-m-d H:i:s');
                $formattedFinishDay = (new DateTime($dayFinished))->setTime(date('H'), date('i'), date('s'))->format('Y-m-d H:i:s');

                $term = "register_date BETWEEN '{$formattedDate}' AND '{$formattedFinishDay}'";

                $result = $this->search($term)->sort("register_date ASC")->result(true);
                $count = $this->search($term, null, 'COUNT(id) as Qty')->result();

                return [
                    'responseCode'=> 200,
                    'data' =>[
                        'query-days-search' => $daysSearch == 4 ? '5' : $days,
                        'query-day-start' => $formattedDate,
                        'query-day-end' => $formattedFinishDay,
                        'count' => $count->Qty,
                        'registers' => $result,
                    ]];
            }

            return [
                'responseCode'=> 404,
                'data' => [
                    'error' => 'FAILED TO FETCH RECORDS'
                ]
            ];

        } catch (\PDOException $ex) {

            $error = 'Error => code: '.$ex->getCode().' | description: '.$ex->getMessage();

            return [
                'responseCode'=> 500,
                'data' => [
                    'error' => $error
                ]
            ];

        }

    }

    /**
     * Verifies the validity of an API key.
     *
     * This method checks if the provided API key exists in the `UserModel` table, ensuring the request is authorized.
     *
     * @param string $key The API key to validate.
     * @return bool True if the key is valid, otherwise false.
     */
    public function checkApiKey(string $key): bool
    {
        $userDb = new UserModel();
        $findApiKey = $userDb->search("api_key = ".$key)->result(true);

        return (bool)$findApiKey;
    }

}