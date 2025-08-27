<?php

namespace App\DTOs\V1\User\Common;

use App\DTOs\Common\AbstractDTO;
use Illuminate\Support\Str;

class GetConfigurationDTO extends AbstractDTO
{
    protected ?string $platform;

    protected ?string $version;

    protected ?array $loginOptions;

    final public function getPlatform(): ?string
    {
        return $this->platform ? Str::lower($this->platform) : null;
    }

    final public function getVersion(): ?string
    {
        return $this->version;
    }

    final public function getLoginOptions(): ?array
    {
        return $this->loginOptions;
    }

    final public function toArray(): array
    {
        return [
            'platform' => $this->platform,
            'version' => $this->version,
            'login_options' => $this->loginOptions,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->platform = $data['platform'] ?? null;
        $this->version = $data['version'] ?? null;
        $this->loginOptions = $data['login_options'] ?? null;

        return true;
    }
}
