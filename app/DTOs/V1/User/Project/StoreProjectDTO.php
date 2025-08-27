<?php

namespace App\DTOs\V1\User\Project;

use App\DTOs\Common\AbstractDTO;
use App\Http\Requests\V1\User\Project\StoreProjectRequest;

class StoreProjectDTO extends AbstractDTO
{
    protected ?string $name;

    protected ?string $description;

    protected ?string $image;

    protected ?int $userId;

    final public function getName(): ?string
    {
        return $this->name;
    }

    final public function getDescription(): ?string
    {
        return $this->description;
    }

    final public function getImageUrl(): ?string
    {
        return $this->image;
    }

    final public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'user_id' => auth()->user()->id,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->name = getIfSet($data, 'name');
        $this->description = getIfSet($data, 'description');
        $this->image = getIfSet($data, 'image');

        return true;
    }

    public static function fromRequest(StoreProjectRequest $request): self
    {
        return new self($request->validated());
    }
}
