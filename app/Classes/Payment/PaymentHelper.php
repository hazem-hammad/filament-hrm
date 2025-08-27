<?php

/**
 * PaymentHelper Trait
 *
 * This trait provides a mechanism to send HTTP requests to a payment gateway with a built-in retry mechanism.
 * It handles retries for 500 server errors, allowing configurable parameters for the number of retries and
 * the delay between attempts. Successful responses are parsed and returned, while errors are managed gracefully.
 */

namespace App\Classes\Payment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait PaymentHelper
{
    /**
     * Send an HTTP request to a payment gateway with retry mechanism.
     *
     * @param  string  $method  HTTP method (e.g., 'GET', 'POST', 'PUT', etc.)
     * @param  string  $baseUri  Base URL of the payment gateway
     * @param  string  $endpoint  API endpoint
     * @param  array  $options  Request options (e.g., headers, body, query parameters)
     * @param  int  $maxRetries  Number of retry attempts
     * @param  int  $retryDelay  Delay between retries in milliseconds
     * @return array Response data or error details
     */
    private function httpCall(string $method, string $baseUri, string $endpoint, array $options = [], int $maxRetries = 1, int $retryDelay = 1000): array
    {
        $client = new Client(['base_uri' => $baseUri]);
        $attempts = 0;
        while ($attempts < $maxRetries) {
            try {
                $response = $client->request($method, $endpoint, $options);

                // Parse and return the response if successful
                return $this->parseResponse($response);
            } catch (RequestException $exception) {
                $attempts++;
                // Check if retry is applicable
                if (
                    $exception->hasResponse() &&
                    $exception->getResponse()->getStatusCode() === 500 &&
                    $attempts < $maxRetries
                ) {
                    // Log retry attempt (optional)
                    logger()->warning("Retrying request due to 500 error. Attempt: {$attempts}");
                    // Delay before retrying
                    usleep($retryDelay * 1000);
                } else {
                    // Return error response
                    return [
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'status_code' => $exception->getCode(),
                        'response' => $exception->hasResponse() ? $this->parseResponse($exception->getResponse()) : null,
                    ];
                }
            }
        }

        // Fallback response if all retries fail
        return [
            'success' => false,
            'message' => 'Maximum retry attempts reached.',
            'status_code' => 500,
            'response' => null,
        ];
    }

    /**
     * Parse the HTTP response.
     *
     * @return array Parsed response data
     */
    private function parseResponse(ResponseInterface $response): array
    {
        return [
            'success' => true,
            'message' => null,
            'status_code' => $response->getStatusCode(),
            'response' => json_decode($response->getBody()->getContents(), true),
        ];
    }

    public function getCurrencyDecimalPoints($currency)
    {
        $decimalPoint = 2;
        $arrCurrencies = [
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        ];
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }

        return $decimalPoint;
    }

    public function convertFortAmount($amount, $currency)
    {
        $total = $amount;
        $decimalPoints = $this->getCurrencyDecimalPoints($currency);
        Log::info('ConvertFortAmount', ['total' => $total, 'decimalPoints' => $decimalPoints, 'currency' => $currency, 'round' => round($total, $decimalPoints), 'pow' => (pow(10, $decimalPoints)), 'return' => (int) round((round($total, $decimalPoints) * pow(10, $decimalPoints)))]);

        return (int) round((round($total, $decimalPoints) * pow(10, $decimalPoints)));
    }
}
