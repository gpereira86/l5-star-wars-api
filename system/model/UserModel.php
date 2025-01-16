<?php

namespace system\model;

use system\core\LogDbModel;

/**
 * Class UserModel
 *
 * Represents the user model which interacts with the 'users' table in the database.
 * This class extends the LogDbModel to perform database operations with logging functionality.
 *
 * @package system\model
 */
class UserModel extends LogDbModel
{
    /**
     * UserModel constructor.
     *
     * Initializes the UserModel by calling the parent constructor and specifying the 'users' table
     * for database interactions. The constructor of the parent LogDbModel class will handle the
     * database connection and any related operations for the 'users' table.
     */
    public function __construct()
    {
        parent::__construct('users');
    }
}
