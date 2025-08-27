<?php

namespace App\Classes\Payment\Responses\Payfort;

use App\Classes\Payment\Constants\PaymentStatus;
use App\Classes\Payment\Responses\PaymentResponseInterface;

class InitialTokenResponse implements PaymentResponseInterface
{
    private bool $success;

    private ?string $message;

    private ?array $data;

    public function __construct(bool $success, ?string $message, ?array $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function isSuccess(): bool
    {
        return $this->success && ($this->data['status'] ?? null) == PaymentStatus::SDK_TOKEN_CREATION_SUCCESS;
    }

    public function getMessage(): ?string
    {
        return $this->message ?: ($this->data['response_message'] ?? '');
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    // Convert to array
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function getSdkToken(): ?string
    {
        return ! $this->isSuccess() ? null : ($this->data['sdk_token'] ?? null);
    }
}
