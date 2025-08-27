<?php

namespace App\DTOs\V1\User\Auth;

use App\DTOs\Common\AbstractDTO;

class RegisterUserDTO extends AbstractDTO
{
    protected string $userName;

    protected string $firstName;

    protected string $lastName;

    protected string $password;

    protected string $birthdate;

    protected array $languages = [];

    protected ?string $image;

    protected string $userType;

    protected string $verificationToken;

    protected int $nationalityId;

    protected ?string $email;

    public function getUsername(): string
    {
        return $this->userName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBirthdate(): string
    {
        return formatDate($this->birthdate);
    }

    public function getNationalityId(): int
    {
        return $this->nationalityId;
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getVerificationToken(): string
    {
        return $this->verificationToken;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->userName,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'password' => $this->password,
            'birthdate' => $this->birthdate,
            'nationality_id' => $this->nationalityId,
            'languages' => $this->languages,
            'image' => $this->image,
            'userType' => $this->userType,
            'verification_token' => $this->verificationToken,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->userName = $data['username'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->password = $data['password'];
        $this->birthdate = $data['birthdate'];
        $this->nationalityId = $data['nationality_id'];
        $this->languages = $data['languages'];
        $this->image = $data['image'] ?? null;
        $this->userType = $data['user_type'];
        $this->verificationToken = $data['verification_token'];

        return true;
    }
}
