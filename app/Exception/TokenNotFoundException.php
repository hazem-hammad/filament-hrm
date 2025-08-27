<?php

namespace App\Exception;

class TokenNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('token_not_found'), 0);
    }
}
