<?php

namespace App\Services\User\Common;

use App\DTOs\V1\User\Common\GetPageDTO;
use App\Repositories\Interfaces\InformationRepositoryInterface;

class InformationService
{
    public function __construct(protected InformationRepositoryInterface $InformationRepository) {}

    final public function getFaqs()
    {
        return $this->InformationRepository->getFaqs();
    }

    final public function getPageBySlug(GetPageDTO $dto)
    {
        return $this->InformationRepository->getPageBySlug($dto->getPageSlug());
    }
}
