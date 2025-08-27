<?php

namespace App\Exception;

class WrongPasswordException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('wrong_password'), 0);
    }
}
