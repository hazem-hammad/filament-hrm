<?php

namespace App\Http\Controllers\Api\V1\User\ContactUs;

use App\DTOs\V1\User\ContactUs\ContactUsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use App\Services\User\ContactUs\ContactUsService;

class ContactUsController extends Controller
{
    public function __construct(
        private readonly ContactUsService $contactUsService,
    ) {}

    public function store(ContactUsRequest $request)
    {
        $dto = new ContactUsDTO($request->validated());
        $this->contactUsService->store($dto);

        return response()->json(['message' => __('Contact Us request submitted successfully.')], 201);
    }
}
