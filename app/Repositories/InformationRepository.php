<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Models\Page;
use App\Repositories\Interfaces\InformationRepositoryInterface;

class InformationRepository implements InformationRepositoryInterface
{
    public function getFaqs()
    {
        return Faq::query()->active()->get();
    }

    public function getPageBySlug(string $slug)
    {
        return Page::where('slug', $slug)->active()->first();

    }
}
