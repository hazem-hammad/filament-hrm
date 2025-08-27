<?php

namespace App\Classes\Payment\DTOs\Payfort;

use App\Classes\Payment\DTOs\RefundDTOInterface;
use App\DTOs\Common\AbstractDTO;
use App\Models\Booking;

class RefundDTO extends AbstractDTO implements RefundDTOInterface
{
    private $transactionId;

    private $merchantReference;

    private $amount;

    private $currency;

    private $refundDescription;

    private $bookingId;

    /**
     * Convert DTO to array representation
     */
    final public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'merchant_reference' => $this->merchantReference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'refund_description' => $this->refundDescription,
            'booking_id' => $this->bookingId,
        ];
    }

    /**
     * Map input data to DTO properties
     */
    final protected function map(array $data): bool
    {
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

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getRefundDescription()
    {
        return $this->refundDescription;
    }

    public function getBookingId()
    {
        return $this->bookingId;
    }

    public function setBookingData(Booking $booking)
    {
        $this->transactionId = $booking->order->fort_id;
        $this->merchantReference = $booking->order->transaction_id;
        $this->amount = $booking->total_price;
        $this->currency = 'SAR';
        $this->bookingId = $booking->id;
        $this->refundDescription = 'Refund Booking '.$booking->id;

        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
