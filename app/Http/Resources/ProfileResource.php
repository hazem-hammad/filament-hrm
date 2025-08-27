<?php

namespace App\Http\Resources;

use App\Enum\MediaCollections;
use App\Enum\MediaTypes;
use App\Http\Resources\Common\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function __construct($resource, private ?string $token = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'bio' => $this->bio,
            'email' => $this->email,
            'is_favorite' => $this->is_favorited,
            'is_verified' => ! is_null($this->email_verified_at),
            'complete_profile_step' => $this->complete_profile_step,
            'is_completed_profile' => $this->isCompletedProfile(),
            'birthdate' => $this->birthdate ? formatDate($this->birthdate, 'Y-m-d', 'd-m-Y') : null,
            'user_type' => $this->type,
            'languages' => $this->languages_ids,
            'access_token' => $this->token ?? null,
            'image' => new MediaResource($this->getFirstMedia(MediaCollections::AVATAR->value)),
            'nationality_id' => $this->nationality_id,
            'experience_years' => $this->experience_years,
            'session_count' => 0, // Placeholder for session count, to be replaced with actual logic if needed
            'earnings' => 0, // Placeholder for earnings, to be replaced with actual logic if needed
            'available_balance' => (float) $this->available_balance,
        ];
    }
}
