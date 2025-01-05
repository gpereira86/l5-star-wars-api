<?php

namespace system\model;

use system\core\LogDbModel;

class LogModel extends LogDbModel
{

    public function __construct()
    {
        parent::__construct('api_logs');
    }

}