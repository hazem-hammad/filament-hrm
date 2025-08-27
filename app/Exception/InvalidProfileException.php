<?php

namespace App\Exception;

class InvalidProfileException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('You can only view your own profile'), 0);
    }
}
