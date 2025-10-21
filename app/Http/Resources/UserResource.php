<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            return [
                'id' =>$this->id,
                'firstName' => $this->name,
                'lastName' => $this->last_name,
                'email' => $this->email,
                'role' => $this->role,
                'profilePictureUrl' => $this->getMedia('image')->first()?->getUrl() ?? null,
                'apple_expires_date' => $this->apple_expires_date->format('Y-m-d') ?? null,
                'apple_is_subscribe' => $this->apple_is_subscribe,
                // 'accessToken' => $this->createToken('app')->plainTextToken,
                // 'type' => 'Bearer',
            ];
    }
}
