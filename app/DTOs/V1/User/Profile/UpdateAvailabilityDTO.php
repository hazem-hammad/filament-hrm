<?php

namespace App\DTOs\V1\User\Profile;

use App\DTOs\Common\AbstractDTO;

class UpdateAvailabilityDTO extends AbstractDTO
{
    protected array $availabilities;

    final public function getAvailabilities(): array
    {
        return $this->availabilities;
    }

    public function toArray(): array
    {
        return [
            'availabilities' => $this->availabilities,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->availabilities = getIfSet($data, 'availabilities', []);

        return true;
    }
}
