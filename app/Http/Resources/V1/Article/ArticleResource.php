<?php

namespace App\Http\Resources\V1\Article;

use App\Enum\MediaCollections;
use App\Http\Resources\Common\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'date' => $this->date?->toDateString(),
            'image' => new MediaResource($this->getFirstMedia(MediaCollections::ARTICLE->value)),
        ];
    }
}
