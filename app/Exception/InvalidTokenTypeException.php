<?php

namespace App\Exception;

class InvalidTokenTypeException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('invalid_token_type'), 0);
    }
}
