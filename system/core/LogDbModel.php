<?php

namespace system\core;

use system\core\DbConection;

/**
 * Abstract class for logging data to a database.
 *
 * The `LogDbModel` class is responsible for logging data into a database, ensuring that the data is validated and processed before being stored.
 *
 * Features:
 * - Validates log data (via `validateLogData` method).
 * - Registers data into the database (via `register` method).
 * - Filters and sanitizes the data before saving (via `dataFilter` method).
 *
 * This class uses `DbConection` to connect to the database and execute SQL queries.
 */
abstract class LogDbModel
{

    protected $erro;
    protected $tabela;

    /**
     * Constructor method for `LogDbModel` class.
     *
     * Initializes the class with the table name where the logs will be saved.
     *
     * @param string $tabela The name of the table in the database.
     */
    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
    }

    /**
     * Filters the data set before inserting it into the database.
     *
     * This method applies `filter_var` to each value to ensure the data is properly formatted.
     *
     * @param array $dataSet The data to be filtered.
     * @return array The filtered data set.
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
     * Validates the log data before saving it to the database.
     *
     * This method checks that required fields such as `request_method`, `endpoint`, and `response_code` are present.
     *
     * @param array $data The log data to be validated.
     * @return array An array of error messages if validation fails, or an empty array if validation is successful.
     */
    public function validateLogData(array $data): array
    {

        $errors = [];

        if (empty($data['request_method'])) {
            $errors[] = 'request_method is required';
        }

        if (empty($data['endpoint'])) {
            $errors[] = 'endpoint is required';
        }

        if (empty($data['response_code'])) {
            $errors[] = 'response_code is required';
        }

        return $errors;

    }

    /**
     * Registers the log data into the database.
     *
     * This method builds an SQL `INSERT` query and executes it using the provided data. If successful, it returns the last inserted ID.
     *
     * @param array $data The log data to be saved.
     * @return int|null The last inserted ID on success, or null on failure.
     */
    protected function register(array $data)
    {

        try {
            $columns = implode(',', array_keys($data));
            $values = ':' . implode(',:', array_keys($data));

            $query = "INSERT INTO " . $this->tabela . "({$columns}) VALUES ({$values})";

            $stmt = DbConection::getInstance()->prepare($query);
            $stmt->execute($this->dataFilter($data));

            return DbConection::getInstance()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->erro = $ex->getCode();
            return null;
        }
    }

    /**
     * Saves the log data by calling the `register` method.
     *
     * This method is a wrapper for the `register` method, allowing a simplified interface for saving data.
     *
     * @param array $data The log data to be saved.
     * @return int|null The last inserted ID on success, or null on failure.
     */
    public function save(array $data)
    {
        return $this->register($data);
    }
}
