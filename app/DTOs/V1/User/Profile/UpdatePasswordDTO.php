<?php

namespace App\DTOs\V1\User\Profile;

use App\DTOs\Common\AbstractDTO;

class UpdatePasswordDTO extends AbstractDTO
{
    protected ?string $old_password;

    protected ?string $password;

    final public function getOldPassword(): ?string
    {
        return $this->old_password;
    }

    final public function getPassword(): ?string
    {
        return $this->password;
    }

    final public function toArray(): array
    {
        return [
            'old_password' => $this->old_password,
            'password' => $this->password,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->old_password = $data['old_password'];
        $this->password = $data['password'];

        return true;
    }
}
