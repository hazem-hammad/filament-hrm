<?php

namespace App\Exception;

class FailedWebhookException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('Failed webhook'), 0);
    }
}
