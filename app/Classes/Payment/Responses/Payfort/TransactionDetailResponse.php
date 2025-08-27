<?php

namespace App\Classes\Payment\Responses\Payfort;

use App\Classes\Payment\Constants\PaymentStatus;
use App\Classes\Payment\Responses\PaymentResponseInterface;

class TransactionDetailResponse implements PaymentResponseInterface
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
        return $this->success && in_array(($this->data['transaction_status'] ?? null), [PaymentStatus::PURCHASE_SUCCESS, PaymentStatus::CAPTURE_SUCCESS]);
    }

    public function isPending(): bool
    {
        return $this->success && ($this->data['transaction_status'] ?? null) == PaymentStatus::TRANSACTION_PENDING;
    }

    public function getMessage(): ?string
    {
        return $this->message ?? ($this->data['transaction_message'] ?? ($this->data['response_message'] ?? ''));
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
        return $this->data['captured_amount'] / 100;
    }
}
