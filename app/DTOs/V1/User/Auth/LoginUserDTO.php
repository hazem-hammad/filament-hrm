<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

class LoginUserDTO extends AbstractDTO
{
    protected ?string $identifier;

    protected ?string $password;

    final public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    final public function getPassword(): ?string
    {
        return $this->password;
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'password' => $this->password,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->identifier = $data['identifier'];
        $this->password = $data['password'];

        return true;
    }
}
