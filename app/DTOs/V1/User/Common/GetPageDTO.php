<?php

namespace App\DTOs\V1\User\Common;

use App\DTOs\Common\AbstractDTO;

class GetPageDTO extends AbstractDTO
{
    protected ?string $page_slug;

    final public function getPageSlug(): ?string
    {
        return $this->page_slug;
    }

    final public function toArray(): array
    {
        return [
            'page_slug' => $this->page_slug,
        ];
    }

    final protected function map(array $data): bool
    {
        $this->page_slug = $data['page_slug'];

        return true;
    }
}
