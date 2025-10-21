<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sender' => ProfileResource::make($this->user),
            'text' => $this->text,
            'is_read' => $this->is_read,
            'images' => MediaResource::collection($this->getMedia('images')),
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
