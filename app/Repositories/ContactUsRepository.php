<?php

namespace App\Repositories;

use App\Models\ContactUs;
use App\Repositories\Interfaces\ContactUsRepositoryInterface;

class ContactUsRepository implements ContactUsRepositoryInterface
{
    public function store($data)
    {
        return ContactUs::create([
            'name' => $data->getName(),
            'email' => $data->getEmail(),
            'subject' => $data->getSubject(),
            'message' => $data->getMessage(),
        ]);
    }
}
