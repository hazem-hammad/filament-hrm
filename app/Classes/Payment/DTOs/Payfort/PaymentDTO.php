<?php

/**
 * PaymentDTO class is a Data Transfer Object (DTO) for handling payment gateway information.
 * It extends the AbstractDTO class and includes methods for setting and retrieving the payment gateway.
 * The class ensures that the gateway is initialized before it can be accessed, throwing an exception if not.
 * The toArray method is defined for converting the DTO into an array representation,
 * and the map method is provided for mapping input data to the DTO properties.
 */

namespace App\Classes\Payment\DTOs\Payfort;

use App\Classes\Payment\Enum\Payments;
use App\DTOs\Common\AbstractDTO;
use App\DTOs\Common\DtoInterface;

class PaymentDTO extends AbstractDTO implements DtoInterface
{
    // Property to hold the payment gateway information
    private $gateway;

    /**
     * Convert DTO to array representation
     *
     * @return array The array representation of the DTO
     */
    final public function toArray(): array
    {
        // Currently returns an empty array; implement conversion logic as needed
        return [];
    }

    /**
     * Map input data to DTO properties
     *
     * @param  array  $data  The input data to map
     * @return bool Returns true if mapping is successful; otherwise, false
     */
    final protected function map(array $data): bool
    {
        // Implement mapping logic based on input data
        return true;
    }

    /**
     * Set the payment gateway to Payfort
     *
     * @return $this Returns the current instance for method chaining
     */
    public function setPayfortAsGateway()
    {
        $this->gateway = Payments::PAYFORT; // Set the gateway to Payfort

        return $this; // Allows for method chaining
    }

    /**
     * Get the initialized payment gateway
     *
     * @return mixed The payment gateway
     *
     * @throws \Exception If the gateway is not initialized
     */
    public function getGateway()
    {
        // Check if the gateway is initialized
        if (! $this->gateway) {
            throw new \Exception('Gateway is not initialized. Please ensure that the gateway is set before calling this method.');
        }

        return $this->gateway; // Return the initialized gateway
    }
}
