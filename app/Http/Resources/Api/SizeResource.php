<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'link' => $this->link,
            'key' => $this->key,
            'image' => $this->getMedia('image')?->first()?->getUrl() ?? null,
            'video' => $this->getMedia('video')?->first()?->getUrl() ?? null,
        ];
    }
}
