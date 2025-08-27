<?php

namespace App\DTOs\V1\User\Profile;

use App\DTOs\Common\AbstractDTO;

class StoreServiceDTO extends AbstractDTO
{
    protected ?array $services;

    final public function getServices(): ?array
    {
        return $this->services;
    }

    public function toArray(): array
    {
        return [
            'services' => $this->services,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->services = getIfSet($data, 'services', []);

        return true;
    }
}
