<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

final class CheckUserDTO extends AbstractDTO
{
    protected string $phone;
    protected string $countryCode;

    protected function map(array $data): bool
    {
        $this->phone = $data['phone'];
        $this->countryCode = $data['country_code'];
        return true;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'country_code' => $this->countryCode,
        ];
    }
}
