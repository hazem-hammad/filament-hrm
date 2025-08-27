<?php

namespace App\Exception;

class UserSuspendedException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('suspended_user'), 0);
    }
}
