<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEndpointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'description' => $this->description,
            'events' => $this->events,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'secret' => $this->whenHas('plainSecret', $this->plainSecret ?? null),
        ];
    }
}
