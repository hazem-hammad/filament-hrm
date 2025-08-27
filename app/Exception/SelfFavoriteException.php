<?php

namespace App\Exception;

class SelfFavoriteException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('You cannot favorite yourself'), 0);
    }
}
