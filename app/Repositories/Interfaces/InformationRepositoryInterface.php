<?php

namespace App\Repositories\Interfaces;

interface InformationRepositoryInterface
{
    public function getFaqs();

    public function getPageBySlug(string $slug);
}
