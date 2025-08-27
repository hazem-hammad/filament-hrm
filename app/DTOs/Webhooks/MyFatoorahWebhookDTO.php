<?php

namespace App\DTOs\Webhooks;

use App\DTOs\Common\AbstractDTO;

class MyFatoorahWebhookDTO extends AbstractDTO
{
    protected string $event;

    protected int $invoiceId;

    protected string $invoiceReference;

    protected string $paymentMethod;

    protected string $transactionStatus;

    protected string $userDefinedField;

    protected array $data;

    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'invoiceId' => $this->invoiceId,
            'invoiceReference' => $this->invoiceReference,
            'paymentMethod' => $this->paymentMethod,
            'transactionStatus' => $this->transactionStatus,
            'userDefinedField' => $this->userDefinedField,
            'data' => $this->data,
            'signature' => request()->headers->get('myfatoorah-signature'),
        ];
    }

    final protected function map(array $data): bool
    {
        $this->event = $data['Event'];
        $this->data = $data['Data'];
        $this->invoiceId = $data['Data']['InvoiceId'];
        $this->invoiceReference = $data['Data']['InvoiceReference'];
        $this->paymentMethod = $data['Data']['PaymentMethod'];
        $this->transactionStatus = $data['Data']['TransactionStatus'];
        $this->userDefinedField = $data['Data']['UserDefinedField'];

        return true;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    public function getInvoiceReference(): string
    {
        return $this->invoiceReference;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getTransactionStatus(): string
    {
        return $this->transactionStatus;
    }

    public function getUserDefinedField(): string
    {
        return $this->userDefinedField;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
