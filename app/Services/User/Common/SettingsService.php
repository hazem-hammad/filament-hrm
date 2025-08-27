<?php

namespace App\Services\User\Common;

final class SettingsService
{
    public function getForceUpdateInfo(string $platform, string $currentVersion): array
    {
        $minVersion = $this->getMinimumVersion($platform);
        $hasForceUpdate = $this->shouldForceUpdate($currentVersion, $minVersion);
        
        return [
            'min_version' => $minVersion,
            'has_force_update' => $hasForceUpdate,
        ];
    }

    private function getMinimumVersion(string $platform): string
    {
        $key = match (strtolower($platform)) {
            'ios' => 'ios_min_version',
            'android' => 'android_min_version',
            default => 'ios_min_version', // default fallback
        };

        return get_setting($key, '1.0.0');
    }

    private function shouldForceUpdate(string $currentVersion, string $minVersion): bool
    {
        // Remove any non-numeric characters and compare versions
        $current = $this->normalizeVersion($currentVersion);
        $minimum = $this->normalizeVersion($minVersion);
        
        return version_compare($current, $minimum, '<');
    }

    private function normalizeVersion(string $version): string
    {
        // Extract version numbers (e.g., "1.2.3" from "v1.2.3" or "1.2.3-beta")
        preg_match('/(\d+(?:\.\d+)*)/', $version, $matches);
        return $matches[1] ?? '0.0.0';
    }
}