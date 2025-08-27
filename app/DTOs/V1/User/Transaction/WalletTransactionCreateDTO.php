<?php

namespace App\DTOs\V1\User\Transaction;

class WalletTransactionCreateDTO
{
    private int $userId;

    private int $transactionId;

    private string $type;

    private float $amount;

    public function __construct(int $userId, int $transactionId, string $type, float $amount)
    {
        $this->userId = $userId;
        $this->transactionId = $transactionId;
        $this->type = $type;
        $this->amount = $amount;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
