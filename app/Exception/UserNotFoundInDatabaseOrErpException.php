<?php

namespace App\Exception;

class UserNotFoundInDatabaseOrErpException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('User not found in database or ERP system'), 0);
    }
}
