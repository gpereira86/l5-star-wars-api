<?php

namespace system\model;

use system\core\LogDbModel;

/**
 * Represents a model for logging API operations.
 *
 * This class extends the `LogDbModel` class and is initialized with a specific database table for logging API-related data.
 * It provides functionality to log various events and operations related to the API, helping with debugging, monitoring,
 * and auditing purposes.
 *
 * The logs are stored in the `api_logs` table in the database.
 *
 * @package system\model
 * @extends LogDbModel
 */
class LogModel extends LogDbModel
{
    /**
     * Initializes the LogModel class and sets the database table to store log data.
     *
     * The constructor calls the parent constructor of the `LogDbModel` class and provides the name of the table (`api_logs`)
     * that will be used to store the log entries. This table will hold data related to the operations performed through the API.
     *
     * This model helps keep track of API requests, responses, errors, and other significant events, which can be useful for
     * debugging and auditing purposes.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('api_logs');
    }
}
