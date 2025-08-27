<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

class VerifyOtpDto extends AbstractDTO
{
    protected string $verificationToken;

    protected string $code;

    final public function toArray(): array
    {
        return [
            'verification_token' => $this->verificationToken,
            'code' => $this->code,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->verificationToken = $data['verification_token'];
        $this->code = $data['otp'];

        return true;
    }

    final public function getVerificationToken(): string
    {
        return $this->verificationToken;
    }

    final public function getCode(): string
    {
        return $this->code;
    }
}
