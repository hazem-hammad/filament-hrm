<?php

namespace App\Exception;

class CannotUseOldPasswordException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('cannot_use_old_password'), 0);
    }
}
