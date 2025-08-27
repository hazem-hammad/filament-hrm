<?php

namespace App\DTOs\V1\User\ContactUs;

use App\DTOs\Common\AbstractDTO;

class ContactUsDTO extends AbstractDTO
{
    protected ?string $name;

    protected ?string $email;

    protected ?string $subject;

    protected ?string $message;

    final public function getName(): ?string
    {
        return $this->name;
    }

    final public function getEmail(): ?string
    {
        return $this->email;
    }

    final public function getSubject(): ?string
    {
        return $this->subject;
    }

    final public function getMessage(): ?string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->email,
            'topic' => $this->subject,
            'details' => $this->message,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->subject = $data['subject'];
        $this->message = $data['message'];

        return true;
    }
}
