<?php

namespace App\Exception;

class DifferentSocialMethodException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('You should login with the same social method'), 0);
    }
}
