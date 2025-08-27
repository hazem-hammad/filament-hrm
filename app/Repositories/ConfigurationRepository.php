<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\ConfigurationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function getSettingsValue(string $key): ?string
    {
        $setting = Setting::query()
            ->where('key', $key)
            ->first();

        return $setting?->value;
    }

    public function getSettingsGroup(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    public function getSettings(): Collection
    {
        return Setting::all();
    }
}
