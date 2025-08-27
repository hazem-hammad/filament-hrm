<?php

namespace App\DTOs\V1\User\Project;

use App\DTOs\Common\AbstractDTO;
use App\Http\Requests\V1\User\Project\ProjectRequest;

class ProjectDTO extends AbstractDTO
{
    public function toArray(): array
    {
        return [];
    }

    protected function map(array $data): bool
    {

        return true;
    }

    public static function fromRequest(ProjectRequest $request): self
    {
        return new self($request->validated());
    }
}
