<?php

namespace App\Classes\Payment\DTOs\MyFatoorah;

use App\Classes\Payment\Constants\PaymentStatus;
use App\Classes\Payment\DTOs\HandleCallbackDTOInterface;
use App\DTOs\Common\AbstractDTO;

class HandleCallbackDTO extends AbstractDTO implements HandleCallbackDTOInterface
{
    private $transactionId;

    private $merchantReference;

    private $amount;

    private $signature;

    private $status;

    private $data;

    private $command;

    /**
     * Convert DTO to array representation
     */
    final public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'merchant_reference' => $this->merchantReference,
            'amount' => $this->amount,
            'signature' => $this->signature,
            'status' => $this->status,
            'data' => $this->data,
            'command' => $this->command,
        ];
    }

    /**
     * Map input data to DTO properties
     */
    final protected function map(array $data): bool
    {
        $this->transactionId = $data['invoiceId'];
        $this->merchantReference = $data['userDefinedField'];
        $this->amount = $data['data']['InvoiceValueInBaseCurrency'];
        $this->status = in_array($data['transactionStatus'], [PaymentStatus::PURCHASE_SUCCESS]);
        $this->data = $data['data'] ?? [];
        $this->command = $data['event'];
        $this->signature = $data['signature'];

        return true;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getMerchantReferenceId()
    {
        return $this->merchantReference;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMessage()
    {
        return $this->data['TransactionStatus'];
    }
}
