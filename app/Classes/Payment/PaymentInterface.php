<?php

namespace App\Classes\Payment;

use App\Classes\Payment\DTOs\GetTransactionDetailDTOInterface;
use App\Classes\Payment\DTOs\HandleCallbackDTOInterface;
use App\Classes\Payment\DTOs\InitialTokenDTOInterface;
use App\Classes\Payment\DTOs\RefundDTOInterface;
use App\Classes\Payment\Responses\PaymentResponseInterface;

interface PaymentInterface
{
    public function refund(RefundDTOInterface $refundDto): PaymentResponseInterface;

    public function initialToken(InitialTokenDTOInterface $initialTokenDTO): PaymentResponseInterface;

    public function getTransactionDetail(GetTransactionDetailDTOInterface $detailDTO): PaymentResponseInterface;

    public function handleCallback(HandleCallbackDTOInterface $callbackDTO): PaymentResponseInterface;
}
