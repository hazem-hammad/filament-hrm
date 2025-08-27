<?php

namespace App\Exception;

class SameOldEmailException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('same_old_email_exception'), 0);
    }
}
