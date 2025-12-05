<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'price' => $this->price,
            'isActive' => $this->is_active, // "is_active" ni "isActive" (camelCase) qilib o'zgartirdik
            'description' => $this->description,
            'createdAt' => $this->created_at->format('d-m-Y H:i:s'), // Sanani formatladik
        ];
    }
}
