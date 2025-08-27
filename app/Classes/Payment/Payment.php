<?php

// This class, Payment, serves as a factory for creating payment gateway instances based on the provided PaymentDTO.
// It maps payment gateways to their respective classes and throws an exception if an unsupported gateway is requested.

namespace App\Classes\Payment;

use App\Classes\Payment\DTOs\MyFatoorah\PaymentDTO;
use App\Classes\Payment\Enum\Payments;
use App\Classes\Payment\Gateways\MyFatoorahGateway;
use App\Classes\Payment\Gateways\PayfortGateway;

class Payment
{
    // Mapping of payment gateway identifiers to their corresponding classes
    public static $payments = [
        Payments::PAYFORT => PayfortGateway::class,
        Payments::MY_FATOORAH => MyFatoorahGateway::class,
    ];

    /**
     * Creates an instance of the payment gateway based on the provided PaymentDTO.
     *
     * @param  PaymentDTO  $paymentDTO  The Data Transfer Object containing payment information.
     * @return object An instance of the payment gateway.
     *
     * @throws \Exception If the requested payment gateway is not supported.
     */
    public static function create(PaymentDTO $paymentDTO)
    {
        // Retrieve the class name for the specified payment gateway
        $paymentClass = self::$payments[$paymentDTO->getGateway()] ?? null;

        // Check if the payment class exists; if not, throw an exception
        if (! $paymentClass) {
            throw new \Exception("Payment gateway '{$paymentDTO->getGateway()}' is not supported. Please check the available gateways.");
        }

        // Instantiate and return the payment gateway class
        return new $paymentClass;
    }
}
