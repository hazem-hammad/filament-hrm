<?php

// This class, InitialTokenDTO, extends an abstract DTO class and is responsible for
// representing the initial token data transfer object in the booking payment domain.
// It includes properties for booking ID and device ID, methods to convert the DTO
// to an array, map input data to the DTO properties, and retrieve the values of
// the device ID and booking ID.

namespace App\Classes\Payment\DTOs\MyFatoorah;

use App\Classes\Payment\DTOs\InitialTokenDTOInterface;
use App\DTOs\Common\AbstractDTO;
use Illuminate\Support\Str;

class InitialTokenDTO extends AbstractDTO implements InitialTokenDTOInterface
{
    private $merchantReference;  // Stores the transaction ID

    private $price;

    /**
     * Convert DTO to array representation
     *
     * @return array The array representation of the DTO
     */
    final public function toArray(): array
    {
        return [
            'price' => $this->price,
            'merchant_reference' => $this->merchantReference,   // Maps merchant reference ID to array
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
        $this->price = $data['price'];
        $this->merchantReference = $data['merchant_reference'] ?? null;

        return true; // Indicate successful mapping
    }

    /**
     * Get the price
     *
     * @return float The price
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the merchant reference ID
     *
     * @return string The merchant reference ID
     */
    public function getMerchantReferenceId(): string
    {
        return $this->merchantReference ?? Str::random(32);
    }
}
