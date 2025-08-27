<?php

namespace App\Exception;

class InvalidUserException extends CustomException
{
    public function __construct()
    {

        parent::__construct(__('User not found'), 0);
    }
}
