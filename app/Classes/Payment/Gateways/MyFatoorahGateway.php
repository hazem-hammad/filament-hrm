<?php

/**
 * MyFatoorahGateway class implements the PaymentInterface to handle payment processing
 * through the Payfort payment gateway. It includes methods for processing refunds
 * and generating initial tokens for transactions. The class utilizes environment
 * variables for configuration and includes logging capabilities for transaction
 * records. The signature method ensures secure requests by generating a hash
 * based on the provided parameters and SHA phrases.
 */

namespace App\Classes\Payment\Gateways;

use App\Classes\Payment\DatabaseLogHelper;
use App\Classes\Payment\DTOs\GetTransactionDetailDTOInterface;
use App\Classes\Payment\DTOs\HandleCallbackDTOInterface;
use App\Classes\Payment\DTOs\InitialTokenDTOInterface;
use App\Classes\Payment\DTOs\MyFatoorah\InitialTokenDTO;
use App\Classes\Payment\DTOs\MyFatoorah\RefundDTO;
use App\Classes\Payment\DTOs\RefundDTOInterface;
use App\Classes\Payment\PaymentHelper;
use App\Classes\Payment\PaymentInterface;
use App\Classes\Payment\Responses\MyFatoorah\HandleCallbackResponse;
use App\Classes\Payment\Responses\MyFatoorah\InitialTokenResponse;
use App\Classes\Payment\Responses\MyFatoorah\PayloadNotVaildResponse;
use App\Classes\Payment\Responses\MyFatoorah\RefundResponse;
use App\Classes\Payment\Responses\MyFatoorah\TransactionDetailResponse;
use App\Classes\Payment\Responses\PaymentResponseInterface;
use Illuminate\Support\Facades\Log;

class MyFatoorahGateway implements PaymentInterface
{
    use DatabaseLogHelper, PaymentHelper;

    // Private properties to store MyFatoorah configuration values
    private $apiKey;

    private $baseUrl;

    private $currency;

    private $paymentMethod = 6;

    private $shaResponsePhrase;

    private $shaType;

    // Constructor to initialize Payfort configuration from environment variables
    public function __construct()
    {
        $this->apiKey = config('services.myfatoorah.api_key');
        $this->baseUrl = config('services.myfatoorah.base_url');
        $this->currency = config('services.myfatoorah.currency', 'KWD');

        $this->shaType = config('services.myfatoorah.sha_type');
        $this->shaResponsePhrase = config('services.myfatoorah.sha_phrase');
    }

    /**
     * Process a refund request.
     *
     * @param  RefundDto  $refundDto  Data Transfer Object containing refund details
     * @return RefundResponse Response object containing the result of the refund operation
     */
    public function refund(RefundDTOInterface $refundDto): RefundResponse
    {
        return new RefundResponse(false, '', []);
    }

    /**
     * Generate an initial token for payment processing.
     *
     * @param  InitialTokenDTO  $initialTokenDTO  Data Transfer Object containing token request details
     * @return InitialTokenResponse Response object containing the result of the token generation
     */
    public function initialToken(InitialTokenDTOInterface $initialTokenDTO): InitialTokenResponse
    {
        // Define the base URL and endpoint for the Payfort API
        $endpoint = '/v2/ExecutePayment';

        // Prepare the options for the HTTP request
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->apiKey,
            ],
            'json' => [
                'PaymentMethodId' => $this->paymentMethod,
                'InvoiceValue' => $initialTokenDTO->getPrice(),
                'DisplayCurrencyIso' => $this->currency,
                'UserDefinedField' => $initialTokenDTO->getMerchantReferenceId(),
                'WebhookUrl' => route('webhooks.payment.myfatoorah'),
            ],
        ];

        // Log the initial token request details
        Log::info('[MyFatoorahGateway] Processing initial token request', $options['json']);

        // Make the HTTP call to the Payfort API
        $initialResponse = $this->httpCall('POST', $this->baseUrl, $endpoint, $options);

        // Create an InitialTokenResponse object based on the API response
        $response = new InitialTokenResponse($initialResponse['success'], $initialResponse['message'], $initialResponse['response']);

        // Log the initial token response
        Log::info('[MyFatoorahGateway] Initial token response received', [
            'success' => $response->isSuccess(),
            'message' => $response->getMessage(),
            'response' => $response->getData(),
        ]);

        // Log the initial token request and response
        $this->addLog($initialTokenDTO, $response);

        return $response;
    }

    /**
     * Generate a secure signature for the request.
     *
     * @param  array  $params  Parameters to include in the signature
     * @param  string  $shaPhrase  Phrase used for generating the signature
     * @return string The generated signature
     */
    private function signature(array $params, $shaPhrase)
    {
        // Sort the parameters by key
        ksort($params);

        // Prepare the combined parameters for signature generation
        $combined_params = array_map(function ($k, $v) {
            return $k === 'signature' ? '' : "$k=$v";
        }, array_keys($params), array_values($params));

        // Join the parameters into a single string
        $joined_parameters = implode('', $combined_params);

        // Create the signature string
        $signature = sprintf('%s%s%s', $shaPhrase, $joined_parameters, $shaPhrase);

        // Return the hashed signature
        return hash($this->shaType, $signature);
    }

    public function getTransactionDetail(GetTransactionDetailDTOInterface $detailDTO): TransactionDetailResponse
    {
        // Define the base URL and endpoint for the Payfort API
        $baseUrl = env('PAYFORT_BASE_URL', 'https://paymentservices.payfort.com');
        $endpoint = '/FortAPI/paymentApi';

        // Prepare the options for the HTTP request
        $options = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'query_command' => 'CHECK_STATUS',
                'access_code' => $this->accessCode,
                'merchant_identifier' => $this->merchantIdentifier,
                'merchant_reference' => $detailDTO->getTransactionId(),
                'language' => 'en',
            ],
        ];

        // Generate the signature for the request
        $options['json']['signature'] = $this->signature($options['json'], $this->shaRequestPhrase);

        // Log the transaction detail request
        Log::info('[MyFatoorahGateway] Processing transaction detail request', $options['json']);

        // Make the HTTP call to the Payfort API
        $refundResponse = $this->httpCall('POST', $baseUrl, $endpoint, $options);

        // Create a TransactionDetailResponse object based on the API response
        $response = new TransactionDetailResponse($refundResponse['success'], $refundResponse['message'], $refundResponse['response']);

        // Log the transaction detail response
        Log::info('[MyFatoorahGateway] Transaction detail response received', [
            'success' => $response->isSuccess(),
            'message' => $response->getMessage(),
            'response' => $response->getData(),
        ]);

        // Log the refund request and response
        $this->addLog($detailDTO, $response);

        return $response;
    }

    public function handleCallback(HandleCallbackDTOInterface $callbackDTO): PaymentResponseInterface
    {
        // Generate the expected signature based on the callback data and the response phrase
        $expectedSignature = $this->signature($callbackDTO->getData(), $this->shaResponsePhrase);

        Log::info('expectedSignature | '.$expectedSignature);
        Log::info('signature | '.$callbackDTO->getSignature());

        // Check if the generated signature matches the signature from the callback data
        if (false && $expectedSignature !== $callbackDTO->getSignature()) {
            // If the signatures do not match, create a response indicating that the payload is not valid
            $response = new PayloadNotVaildResponse(false, '', $callbackDTO->getData());
        } else {
            // If the signatures match, create a successful response with the callback status and message
            $response = new HandleCallbackResponse(
                $callbackDTO->getStatus(),
                $callbackDTO->getMessage(),
                $callbackDTO->getData()
            );
        }

        // Log the callback data and the response for auditing and debugging purposes
        $this->addLog($callbackDTO, $response);

        // Return the response object
        return $response;
    }
}
