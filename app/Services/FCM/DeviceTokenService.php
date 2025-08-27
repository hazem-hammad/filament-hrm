<?php

namespace App\Services\FCM;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service for managing FCM device tokens
 */
class DeviceTokenService
{
    /**
     * Update or create device token for a user
     */
    public function updateDeviceToken(User $user, string $deviceToken, string $platform = 'unknown'): bool
    {
        try {
            // Validate token format
            if (!$this->isValidDeviceToken($deviceToken)) {
                Log::warning('Invalid device token format', [
                    'user_id' => $user->id,
                    'token_length' => strlen($deviceToken)
                ]);
                return false;
            }

            DB::transaction(function () use ($user, $deviceToken, $platform) {
                // Remove old tokens for this user on the same platform
                $user->tokens()
                    ->where('device_token', '!=', $deviceToken)
                    ->where('platform', $platform)
                    ->update(['device_token' => null]);

                // Update or create new token
                $user->tokens()->updateOrCreate(
                    ['device_token' => $deviceToken],
                    [
                        'platform' => $platform,
                        'last_used_at' => now(),
                        'is_active' => true,
                    ]
                );
            });

            Log::info('Device token updated', [
                'user_id' => $user->id,
                'platform' => $platform
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update device token', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove invalid device tokens from database
     */
    public function cleanupInvalidTokens(array $invalidTokens): int
    {
        if (empty($invalidTokens)) {
            return 0;
        }

        $count = DB::table('personal_access_tokens')
            ->whereIn('device_token', $invalidTokens)
            ->update([
                'device_token' => null,
                'is_active' => false,
                'updated_at' => now()
            ]);

        Log::info('Cleaned up invalid device tokens', [
            'count' => $count,
            'tokens' => count($invalidTokens)
        ]);

        return $count;
    }

    /**
     * Get valid device tokens for a user
     */
    public function getValidDeviceTokens(User $user): array
    {
        return $user->tokens()
            ->whereNotNull('device_token')
            ->where('is_active', true)
            ->where('last_used_at', '>', Carbon::now()->subDays(30))
            ->pluck('device_token')
            ->toArray();
    }

    /**
     * Mark tokens as used when notification is sent
     */
    public function markTokensAsUsed(array $deviceTokens): void
    {
        if (empty($deviceTokens)) {
            return;
        }

        DB::table('personal_access_tokens')
            ->whereIn('device_token', $deviceTokens)
            ->update(['last_used_at' => now()]);
    }

    /**
     * Remove old inactive tokens (older than 90 days)
     */
    public function removeStaleTokens(): int
    {
        $count = DB::table('personal_access_tokens')
            ->where('last_used_at', '<', Carbon::now()->subDays(90))
            ->whereNotNull('device_token')
            ->update([
                'device_token' => null,
                'is_active' => false
            ]);

        Log::info('Removed stale device tokens', ['count' => $count]);
        return $count;
    }

    /**
     * Validate device token format
     */
    private function isValidDeviceToken(string $token): bool
    {
        // FCM tokens are typically 140+ characters and contain specific patterns
        return strlen($token) >= 100 && 
               strlen($token) <= 200 && 
               preg_match('/^[a-zA-Z0-9_:-]+$/', $token);
    }
}