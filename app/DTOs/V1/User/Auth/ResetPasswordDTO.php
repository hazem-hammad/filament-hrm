<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

class ResetPasswordDTO extends AbstractDTO
{
    protected ?string $password;

    protected string $verificationToken;

    final public function getPassword(): ?string
    {
        return $this->password;
    }

    final public function getVerificationToken(): string
    {
        return $this->verificationToken;
    }

    public function toArray(): array
    {
        return [
            'verification_token' => $this->verificationToken,
            'password' => $this->password,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->verificationToken = $data['verification_token'];
        $this->password = $data['new_password'];

        return true;
    }
}
