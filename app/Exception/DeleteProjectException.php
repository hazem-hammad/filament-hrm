<?php

namespace App\Exception;

class DeleteProjectException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('Cannot delete a project that has associated properties'), 0);
    }
}
