<?php

namespace App\Http\Resources\Api;

use App\Models\Notepad;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer' => $this->user_id,
            'customer_name' => $this->user->name . ' ' . $this->user->last_name,
            'seamstress' => $this->seamstress_id,
            'seamstress_name' => $this->seamstress->name . ' ' . $this->seamstress->last_name,
            'status' => __($this->status),
            'seamstress_comfirm' => (bool) $this->seamstress_comfirm,
            'customer_comfirm' => (bool) $this->customer_comfirm,
            'status' => $this->status,
            'category' => $this->category_id,
            'category_name' => $this->category->name ?? null,
            'notepad' => NotepadResource::make($this->notepad) ?? null,
            'chat' => MessageResource::collection($this->messages()->latest()->take(1)->get()),
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'statusDescription' => __($this->status),
            // 'notepad' => $this->whenLoaded('notepad')
            'sizes' => OrderSizeResource::collection($this->sizes),
            // 'chat' => MessageResource::collection($this->messages),
        ];
    }
}
