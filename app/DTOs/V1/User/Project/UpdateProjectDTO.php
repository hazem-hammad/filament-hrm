<?php

namespace App\DTOs\V1\User\Project;

use App\DTOs\Common\AbstractDTO;
use App\Http\Requests\V1\User\Project\UpdateProjectRequest;

class UpdateProjectDTO extends AbstractDTO
{
    protected ?string $name;

    protected ?string $description;

    protected ?string $image;

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

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }

    final protected function map(array $data): bool
    {
        $this->name = getIfSet($data, 'name');
        $this->description = getIfSet($data, 'description');
        $this->image = getIfSet($data, 'image');

        return true;
    }

    public static function fromRequest(UpdateProjectRequest $request): self
    {
        return new self($request->validated());
    }
}
