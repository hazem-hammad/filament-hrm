<?php

namespace App\Exception;

class InvalidCredentialsException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('invalid_credentials'), 0);
    }
}
