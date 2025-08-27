<?php

namespace App\Http\Resources;

use App\Enum\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $local = app()->getLocale();

        return [
            'id' => $this->id,
            'title' => $this->data['title'][$local] ?? '',
            'body' => $this->data['body'][$local] ?? '',
            'date' => $this->created_at->diffForHumans(),
            'is_seen' => (bool) $this->read_at,
            'action' => [
                'id' => null,
                'type' => NotificationType::ADMIN->value,
            ],
        ];
    }
}
