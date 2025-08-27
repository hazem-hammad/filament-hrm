<?php

namespace App\Classes\Payment\Responses\MyFatoorah;

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
        return $this->success && ($this->data['IsSuccess'] ?? null);
    }

    public function getMessage(): ?string
    {
        return $this->message ?: ($this->data['Message'] ?? '');
    }

    public function getData(): ?array
    {
        return $this->data['Data'] ?? [];
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

    public function getTransactionId()
    {
        return $this->data['Data']['InvoiceId'] ?? null;
    }

    public function getPaymentUrl()
    {
        return $this->data['Data']['PaymentURL'] ?? null;
    }
}
