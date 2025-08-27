<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyPayPalWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if (! $this->isValidPayPalWebhook($request)) {
            Log::error('PayPal webhook verification failed', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    private function isValidPayPalWebhook(Request $request): bool
    {
        $requiredHeaders = [
            'PAYPAL-TRANSMISSION-ID',
            'PAYPAL-CERT-ID',
            'PAYPAL-TRANSMISSION-SIG',
            'PAYPAL-TRANSMISSION-TIME',
        ];

        foreach ($requiredHeaders as $header) {
            if (! $request->header($header)) {
                Log::warning("PayPal webhook missing header: {$header}");

                return false;
            }
        }

        // Verify with PayPal API
        try {
            $paypal = new \Srmklive\PayPal\Services\PayPal;
            $paypal->setApiCredentials(config('paypal'));
            $paypal->getAccessToken();

            $payload = [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_id' => $request->header('PAYPAL-CERT-ID'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id' => config('paypal.webhook.id'),
                'webhook_event' => json_decode($request->getContent(), true),
            ];

            $result = $paypal->verifyWebhook($payload);

            return ($result['verification_status'] ?? '') === 'SUCCESS';

        } catch (\Exception $e) {
            Log::error('PayPal webhook verification failed', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
