<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Resursni massivga o'girish.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plateNumber' => $this->plate_number, // camelCase formatiga o'giramiz
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'owner' => [ // Ichma-ich obyekt yasaymiz
                'name' => $this->owner_name,
                'phone' => $this->owner_phone,
            ],
        ];
    }
}
