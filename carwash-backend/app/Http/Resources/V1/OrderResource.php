<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'ticketNo' => $this->ticket_no,
            'status' => $this->status,
            'checkinAt' => $this->checkin_at,
            'checkoutAt' => $this->checkout_at,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'note' => $this->note,

            // Bog'liq resurslarni yuklash
            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'manager' => new UserResource($this->whenLoaded('manager')),
            'cashier' => new UserResource($this->whenLoaded('cashier')),

            // Bog'liq resurslar to'plamini yuklash
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
