<?php

namespace App\Http\Resources;

use App\Enum\MediaCollections;
use App\Http\Resources\Common\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct($resource, private ?string $token = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'verified' => ! is_null($this->email_verified_at),
            'user_type' => $this->type,
            'image' => new MediaResource($this->getFirstMedia(MediaCollections::AVATAR->value)),
        ];
    }
}
