<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'firstName' => $this->name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'profile_description' => $this->profile_description,
            'role_seamstress' => $this->is_seamstress,
            'role_customer' => $this->is_customer,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'rating' => $this->whenLoaded('reviews', $this->getRatingAttribute()),
            'email_verified' => $this->hasVerifiedEmail(),
            'profilePictureUrl' => $this->getMedia('image')->first()?->getUrl() ?? null,
            'portfolio' => MediaResource::collection($this->getMedia('portfolio')),
            'sizes' => UserSizeResource::collection($this->sizes),
            'books' => BookResource::collection($this->books),
            'apple_expires_date' => $this->apple_expires_date,
            'apple_is_subscribe' => (bool) $this->apple_is_subscribe,
        ];
    }
}
