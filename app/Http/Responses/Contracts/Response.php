<?php

namespace App\Http\Responses\Contracts;

use Illuminate\Http\JsonResponse;

interface Response
{
    public function toJson(): JsonResponse;
}
