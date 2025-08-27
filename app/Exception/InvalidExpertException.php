<?php

namespace App\Exception;

class InvalidExpertException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('not_found_expert'), 0);
    }
}
