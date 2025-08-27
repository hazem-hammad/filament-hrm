<?php

namespace App\Exception;

class CannotResetPasswordException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('cannot_reset_password'), 0);
    }
}
