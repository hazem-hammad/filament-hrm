<?php

namespace App\Http\Responses;

use App\Http\Responses\Contracts\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

class DataResponse implements Response
{
    /**
     * @return void
     */
    public function __construct(private $data = [], private ?string $message = null, private int $status = HTTPResponse::HTTP_OK, private array $headers = []) {}

    final public function toJson(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'data' => $this->data,
        ], $this->status, $this->headers);
    }
}
