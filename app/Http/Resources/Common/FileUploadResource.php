<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\JsonResource;

class FileUploadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'path' => $this->url,
            'path_storage' => $this->path,
            'mime_type' => $this->mime_type,
        ];
    }
}
