<?php

namespace App\DTOs\V1\User\Transaction;

class TransactionUpdateDTO
{
    private string $status;

    private array $paypalPaymentData;

    public function __construct(string $status, array $paypalPaymentData)
    {
        $this->status = $status;
        $this->paypalPaymentData = $paypalPaymentData;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPaypalPaymentData(): array
    {
        return $this->paypalPaymentData;
    }
}
