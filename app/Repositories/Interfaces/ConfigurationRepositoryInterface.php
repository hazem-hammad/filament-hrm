<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface ConfigurationRepositoryInterface
{
    public function getSettingsValue(string $key): ?string;

    public function getSettingsGroup(string $group): Collection;

    public function getSettings(): Collection;
}
