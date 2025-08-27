<?php

// This class, InitialTokenDTO, extends an abstract DTO class and is responsible for
// representing the initial token data transfer object in the booking payment domain.
// It includes properties for booking ID and device ID, methods to convert the DTO
// to an array, map input data to the DTO properties, and retrieve the values of
// the device ID and booking ID.

namespace App\Classes\Payment\DTOs\MyFatoorah;

use App\Classes\Payment\DTOs\GetTransactionDetailDTOInterface;
use App\DTOs\Common\AbstractDTO;

class GetTransactionDetailDTO extends AbstractDTO implements GetTransactionDetailDTOInterface
{
    private $bookingId; // Stores the booking ID

    private $transactionId;  // Stores the transaction ID

    /**
     * Convert DTO to array representation
     *
     * @return array The array representation of the DTO
     */
    final public function toArray(): array
    {
        return [
            'booking_id' => $this->bookingId, // Maps booking ID to array
            'transaction_id' => $this->transactionId,   // Maps transaction ID to array
        ];
    }

    /**
     * Map input data to DTO properties
     *
     * @param  array  $data  Input data to be mapped
     * @return bool Returns true after mapping the data
     */
    final protected function map(array $data): bool
    {
        // Assign booking ID from input data
        $this->bookingId = $data['booking_id'];
        // Assign transaction ID from input data
        $this->transactionId = $data['transaction_id'];

        return true; // Indicate successful mapping
    }

    /**
     * Get the transaction ID
     *
     * @return string The transaction ID
     */
    public function getTransactionId(): string
    {
        return $this->transactionId; // Return the transaction ID
    }

    /**
     * Get the booking ID
     *
     * @return string The booking ID
     */
    public function getBookingId(): string
    {
        return $this->bookingId; // Return the booking ID
    }
}
