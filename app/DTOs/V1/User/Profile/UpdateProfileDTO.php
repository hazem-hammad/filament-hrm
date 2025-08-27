<?php

namespace App\DTOs\V1\User\Profile;

use App\DTOs\Common\AbstractDTO;

class UpdateProfileDTO extends AbstractDTO
{
    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $image;

    protected ?string $bio;

    protected ?string $birthdate;

    protected ?string $nationality_id;

    protected array $languages = [];

    final public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    final public function getLanguages(): array
    {
        return $this->languages;
    }

    final public function getNationailtyId(): ?string
    {
        return $this->nationality_id;
    }

    final public function getLastName(): ?string
    {
        return $this->lastName;
    }

    final public function getBio(): ?string
    {
        return $this->bio;
    }

    final public function getBirthdate(): ?string
    {
        return formatDate($this->birthdate);
    }

    final public function getImage(): ?string
    {
        return $this->image;
    }

    public function toArray(): array
    {
        return [
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'bio' => $this->bio,
            'image' => $this->image,
            'birthdate' => $this->birthdate,
            'nationality_id' => $this->nationality_id,
            'languages' => $this->languages,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->firstName = getIfSet($data, 'first_name');
        $this->lastName = getIfSet($data, 'last_name');
        $this->bio = getIfSet($data, 'bio');
        $this->image = getIfSet($data, 'image');
        $this->birthdate = getIfSet($data, 'birthdate');
        $this->nationality_id = getIfSet($data, 'nationality_id');
        $this->languages = getIfSet($data, 'languages');

        return true;
    }
}
