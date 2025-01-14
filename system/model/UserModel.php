<?php

namespace system\model;

use system\core\LogDbModel;

/**
 * Represents a model for logging operations, extending the LogDbModel class.
 * The model is initialized with a specific database table for logging data.
 *
 * This class is used to handle logging operations related to the API,
 * storing logs in the 'api_logs' table in the database.
 *
 * @package system\model
 * @extends LogDbModel
 */
class UserModel extends LogDbModel
{
    /**
     * Initializes the class and calls the parent constructor with the name of the log table in your database.
     *
     * This constructor ensures that the `LogDbModel` class is properly initialized with the table
     * 'api_logs' where log data will be stored.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('users');
    }
}
