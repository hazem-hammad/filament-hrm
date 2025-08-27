<?php

namespace App\Exception;

class TokenExpiredException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('token_expired'), 0);
    }
}
