<?php

namespace system\core;

use DateTime;
use Exception;
use system\core\DbConection;
use system\model\UserModel;

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

    protected $table;
    protected $params;
    protected $sort;
    protected $limit;
    protected $offset;
    protected $query;


    /**
     * Constructor method for `LogDbModel` class.
     *
     * Initializes the class with the table name where the logs will be saved.
     *
     * @param string $tabela The name of the table in the database.
     */
    public function __construct(string $table)
    {
        $this->table = $table;
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

    public function sort(string $ordem)
    {
        $this->sort = " ORDER BY {$ordem}";
        return $this;
    }


    public function limit(string $limite)
    {
        $this->limit = " LIMIT {$limite}";
        return $this;
    }


    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }


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
            return 'Error => code: '.$ex->getCode().' | description: '.$ex->getmessage();
        }
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

            $this->query = "INSERT INTO " . $this->table . " ({$columns}) VALUES ({$values})";

            $stmt = DbConection::getInstance()->prepare($this->query);
            $stmt->execute($this->dataFilter($data));

            return DbConection::getInstance()->lastInsertId();
        } catch (\PDOException $ex) {
            return 'Error => code: '.$ex->getCode().' | description: '.$ex->getmessage();
        }
    }


    // ---------------------------------------------

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

    public function getLogRegister(string $days=null, string $finished=null, string $apiKey)
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

            $error = 'Error => code: '.$ex->getCode().' | description: '.$ex->getmessage();

            return [
                'responseCode'=> 500,
                'data' => [
                    'error' => $error
                ]
            ];

        }

    }

    public function checkApiKey(string $key):bool
    {
        $userDb = new UserModel();
        $findApiKey =$userDb->search("api_key = ".$key)->result(true);

        return (bool)$findApiKey;
    }


}
