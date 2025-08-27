<?php

namespace App\Classes\Payment\Responses;

interface PaymentResponseInterface
{
    public function isSuccess(): bool;

    public function getMessage(): ?string;

    public function getData(): ?array;

    public function toArray(): array;
}
