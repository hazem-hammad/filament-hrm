<?php

namespace App\Exception;

class DeletedPropertyException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('Property not found or not deleted'), 0);
    }
}
