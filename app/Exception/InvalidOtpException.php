<?php

namespace App\Exception;

class InvalidOtpException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('invalid_otp'), 0);
    }
}
