<?php

namespace App\Classes\Payment\Responses\Payfort;

use App\Classes\Payment\Responses\PaymentResponseInterface;

class HandleCallbackResponse implements PaymentResponseInterface
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
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function getMerchantReferenceId()
    {
        return $this->data['merchant_reference'];
    }

    public function getTransactionId()
    {
        return $this->data['fort_id'] ?? null;
    }

    public function getAmount()
    {
        return $this->data['amount'] / 100;
    }

    public function getStatus(): ?string
    {
        return $this->data['transaction_status'];
    }
}
