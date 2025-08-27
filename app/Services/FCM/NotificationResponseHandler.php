<?php

namespace App\Services\FCM;

use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;

/**
 * Handle FCM notification responses and errors
 */
class NotificationResponseHandler
{
    public function __construct(
        private DeviceTokenService $deviceTokenService
    ) {}

    /**
     * Handle FCM response and clean up invalid tokens
     */
    public function handleResponse($response, array $deviceTokens): array
    {
        $results = [
            'success_count' => 0,
            'failure_count' => 0,
            'invalid_tokens' => []
        ];

        if (!$response || !isset($response['results'])) {
            Log::warning('Invalid FCM response format');
            return $results;
        }

        foreach ($response['results'] as $index => $result) {
            $deviceToken = $deviceTokens[$index] ?? null;
            
            if (!$deviceToken) {
                continue;
            }

            if (isset($result['message_id'])) {
                // Success
                $results['success_count']++;
                
                // Update canonical registration ID if provided
                if (isset($result['registration_id'])) {
                    $this->handleCanonicalId($deviceToken, $result['registration_id']);
                }
            } else {
                // Failure
                $results['failure_count']++;
                $error = $result['error'] ?? 'Unknown error';
                
                if ($this->isInvalidTokenError($error)) {
                    $results['invalid_tokens'][] = $deviceToken;
                    Log::info('Invalid device token detected', [
                        'token' => substr($deviceToken, 0, 20) . '...',
                        'error' => $error
                    ]);
                }
            }
        }

        // Clean up invalid tokens
        if (!empty($results['invalid_tokens'])) {
            $this->deviceTokenService->cleanupInvalidTokens($results['invalid_tokens']);
        }

        // Log summary
        Log::info('FCM notification results', [
            'success' => $results['success_count'],
            'failures' => $results['failure_count'],
            'invalid_tokens' => count($results['invalid_tokens'])
        ]);

        return $results;
    }

    /**
     * Handle FCM exceptions
     */
    public function handleException(\Exception $exception, array $deviceTokens): void
    {
        if ($exception instanceof CouldNotSendNotification) {
            Log::error('FCM notification failed', [
                'message' => $exception->getMessage(),
                'device_count' => count($deviceTokens)
            ]);

            // If it's an authentication error, log it specifically
            if (str_contains($exception->getMessage(), 'authentication')) {
                Log::critical('FCM authentication failed - check service account credentials');
            }
        } else {
            Log::error('Unexpected notification error', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if error indicates invalid token
     */
    private function isInvalidTokenError(string $error): bool
    {
        $invalidTokenErrors = [
            'InvalidRegistration',
            'NotRegistered',
            'MismatchSenderId',
            'InvalidApnsCredential'
        ];

        return in_array($error, $invalidTokenErrors);
    }

    /**
     * Handle canonical registration ID updates
     */
    private function handleCanonicalId(string $oldToken, string $newToken): void
    {
        // Update the device token in database
        \DB::table('personal_access_tokens')
            ->where('device_token', $oldToken)
            ->update([
                'device_token' => $newToken,
                'updated_at' => now()
            ]);

        Log::info('Updated canonical registration ID', [
            'old_token' => substr($oldToken, 0, 20) . '...',
            'new_token' => substr($newToken, 0, 20) . '...'
        ]);
    }
}