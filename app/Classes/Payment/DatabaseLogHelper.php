<?php

// This trait, DatabaseLogHelper, provides a method to log payment transactions to a database.
// The addLog method takes a Data Transfer Object (DTO) and a payment response interface,
// and based on the type of DTO (InitialTokenDTO or RefundDTO), it constructs a log record
// with relevant transaction details. If the DTO does not match any known type, it logs an error.
// Finally, if a record is created, it is saved to the TransactionLog model.

namespace App\Classes\Payment;

use App\Classes\Payment\DTOs\GetTransactionDetailDTOInterface;
use App\Classes\Payment\DTOs\HandleCallbackDTOInterface;
use App\Classes\Payment\DTOs\InitialTokenDTOInterface;
use App\Classes\Payment\DTOs\RefundDTOInterface;
use App\Classes\Payment\Responses\PaymentResponseInterface;
use App\DTOs\Common\DtoInterface;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\Log;

trait DatabaseLogHelper
{
    /**
     * Adds a log entry for a payment transaction.
     *
     * @param  DtoInterface  $dto  The Data Transfer Object containing transaction details.
     * @param  PaymentResponseInterface  $paymentResponse  The response from the payment process.
     */
    private function addLog(DtoInterface $dto, PaymentResponseInterface $paymentResponse)
    {
        try {
            // Initialize an empty record array to hold log details
            $record = [];

            // Determine the type of DTO and construct the log record accordingly
            switch (true) {
                case $dto instanceof InitialTokenDTOInterface:
                    $record = [
                        'merchant_reference' => $dto->getMerchantReferenceId(),
                        'gateway_transaction_id' => $paymentResponse->getTransactionId(),
                        'type' => 'ExecutePayment',
                        'status' => $paymentResponse->isSuccess(),
                        'request_payload' => $dto->toArray(),
                        'response_payload' => $paymentResponse->toArray(),
                        'message' => $paymentResponse->getMessage(),
                    ];
                    Log::info('[DatabaseLogHelper] Initial Token log created', $record);
                    break;

                case $dto instanceof RefundDTOInterface:
                    $record = [
                        'merchant_reference' => $dto->getMerchantReferenceId(),
                        'gateway_transaction_id' => $paymentResponse->getTransactionId(),
                        'type' => 'REFUND',
                        'status' => $paymentResponse->isSuccess(),
                        'request_payload' => $dto->toArray(),
                        'response_payload' => $paymentResponse->toArray(),
                        'message' => $paymentResponse->getMessage(),
                    ];
                    Log::info('[DatabaseLogHelper] Refund log created', $record);
                    break;

                case $dto instanceof GetTransactionDetailDTOInterface:
                    $record = [
                        'merchant_reference' => $dto->getTransactionId(),
                        'gateway_transaction_id' => $paymentResponse->getTransactionId(),
                        'type' => 'CHECK_STATUS',
                        'status' => $paymentResponse->isSuccess(),
                        'request_payload' => $dto->toArray(),
                        'response_payload' => $paymentResponse->toArray(),
                        'message' => $paymentResponse->getMessage(),
                    ];
                    Log::info('[DatabaseLogHelper] Transaction Detail log created', $record);
                    break;

                case $dto instanceof HandleCallbackDTOInterface:
                    $record = [
                        'merchant_reference' => $dto->getMerchantReferenceId(),
                        'gateway_transaction_id' => $paymentResponse->getTransactionId(),
                        'type' => $paymentResponse->getStatus(),
                        'status' => $paymentResponse->isSuccess(),
                        'request_payload' => $dto->toArray(),
                        'response_payload' => $paymentResponse->toArray(),
                        'message' => $paymentResponse->getMessage(),
                    ];
                    Log::info('[DatabaseLogHelper] Transaction handle callback log created', $record);
                    break;

                default:
                    // Log an error if the DTO type is not recognized
                    Log::error('[DatabaseLogHelper] Unrecognized DTO type for logging.');
                    break;
            }

            // If a record was created, save it to the TransactionLog model
            if (! empty($record)) {
                TransactionLog::create($record);
            }
        } catch (\Exception $exception) {
            Log::error('[DatabaseLogHelper] Failed to add log: '.$exception->getMessage());
        }
    }
}
