<?php

namespace App\Http\Responses;

use App\Http\Responses\Contracts\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

class ErrorResponse implements Response
{
    public function __construct(private ?string $message = null, private $errors = null, private int $status = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR, private array $headers = []) {}

    final public function toJson(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->status, $this->headers);
    }
}
