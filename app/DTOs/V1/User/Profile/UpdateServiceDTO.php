<?php

namespace App\DTOs\V1\User\Profile;

use App\DTOs\Common\AbstractDTO;

class UpdateServiceDTO extends AbstractDTO
{
    protected float $hour_rate;

    protected string $bio;

    final public function getHourRate(): float
    {
        return $this->hour_rate;
    }

    final public function getBio(): string
    {
        return $this->bio;
    }

    public function toArray(): array
    {
        return [
            'hour_rate' => $this->hour_rate,
            'bio' => $this->bio,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->hour_rate = $data['hour_rate'];
        $this->bio = $data['bio'];

        return true;
    }
}
