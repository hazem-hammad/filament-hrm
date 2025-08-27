<?php

namespace App\DTOs\V1\User\Common;

use App\DTOs\Common\AbstractDTO;

class UpdateDeviceTokenDTO extends AbstractDTO
{
    protected ?string $device_token;

    protected ?string $user_language;

    final public function getDeviceToken(): ?string
    {
        return $this->device_token;
    }

    final public function getUserLanguage(): ?string
    {
        return $this->user_language;
    }

    final public function toArray(): array
    {
        return [
            'device_token' => $this->device_token,
            'user_language' => $this->user_language,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->device_token = $data['device_token'];
        $this->user_language = $data['user_language'];

        return true;
    }
}
