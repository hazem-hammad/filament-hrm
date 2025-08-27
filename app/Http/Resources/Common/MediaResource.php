<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'path' => $this->getUrl(),
            'path_thumbnail' => $this->getUrl(),
            'mime_type' => $this->mime_type,
        ];
    }
}
