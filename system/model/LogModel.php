<?php

namespace system\model;

use system\core\LogDbModel;

/**
 * Represents a model for logging operations, extending the LogDbModel class.
 * The model is initialized with a specific database table for logging data.
 */
class LogModel extends LogDbModel
{
    /**
     * Initializes the class and calls the parent constructor with the name of the log table in your database.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('api_logs');
    }


}