<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotepadFolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'model' => class_basename($this->resource),
            'id' => $this->id,
            'notepad_id' => $this->notepad_id,
            'name' => $this->name,
            'texts' => TextResource::collection($this->whenLoaded('texts')),
            'files' => MediaResource::collection($this->getMedia('files')),
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}
