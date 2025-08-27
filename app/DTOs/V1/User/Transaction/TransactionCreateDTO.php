<?php

namespace App\DTOs\V1\User\Transaction;

class TransactionCreateDTO
{
    public ?int $userId = null;

    public ?string $merchantReference = null;

    public ?float $amount = null;

    public ?string $paymentMethod = null;

    public ?string $status = null;

    public ?string $paypalPaymentId = null;

    public ?string $paypalPaymentData = null;

    public function map(array $data): bool
    {
        $this->userId = $data['user_id'] ?? null;
        $this->merchantReference = $data['merchant_reference'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->paymentMethod = $data['payment_method'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->paypalPaymentId = $data['paypal_order_id'] ?? null;
        $this->paypalPaymentData = $data['paypal_payment_data'] ?? null;

        return true;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'merchant_reference' => $this->merchantReference,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'paypal_order_id' => $this->paypalPaymentId,
            'paypal_payment_data' => $this->paypalPaymentData,
        ];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPaypalPaymentId(): ?string
    {
        return $this->paypalPaymentId;
    }

    public function getPaypalPaymentData(): ?string
    {
        return $this->paypalPaymentData;
    }
}
