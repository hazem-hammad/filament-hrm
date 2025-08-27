<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

class SendOtpDto extends AbstractDTO
{
    protected string $identifier;

    protected string $action;

    final public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'action' => $this->action,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->identifier = $data['identifier'];
        $this->action = $data['type'];

        return true;
    }

    final public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    final public function getAction(): ?string
    {
        return $this->action;
    }
}
