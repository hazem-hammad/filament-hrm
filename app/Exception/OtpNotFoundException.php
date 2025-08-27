<?php

namespace App\Exception;

class OtpNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('otp_not_found'), 0);
    }
}
