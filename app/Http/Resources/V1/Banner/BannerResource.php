<?php

namespace App\Http\Resources\V1\Banner;

use App\Enum\MediaCollections;
use App\Http\Resources\Common\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => new MediaResource($this->getFirstMedia(MediaCollections::BANNER->value)),
        ];
    }
}
