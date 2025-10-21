<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'name' => $this->name,
            'author' => $this->author,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'pages' => $this->pages,
            'articles' => $this->articles,
            'image' => $this->getMedia('image')->first()?->getUrl() ?? null,
            'book' => $this->getMedia('book')->first()?->getUrl() ?? null,
            'book_trial' => $this->getMedia('book_trial')->first()?->getUrl() ?? null,
        ];
    }
}
