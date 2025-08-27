<?php

namespace App\Exception;

class OtpExpiredException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('otp_expired'), 0);
    }
}
