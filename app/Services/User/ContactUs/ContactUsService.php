<?php

namespace App\Services\User\ContactUs;

use App\Repositories\Interfaces\ContactUsRepositoryInterface;

class ContactUsService
{
    public function __construct(protected ContactUsRepositoryInterface $contactUsRepository) {}

    public function store($dto)
    {
        return $this->contactUsRepository->store($dto);
    }
}
