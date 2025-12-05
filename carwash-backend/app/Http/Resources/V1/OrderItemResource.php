<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->qty,
            'unitPrice' => $this->unit_price,
            'lineTotal' => $this->line_total,

            // Bu yerda biz 'service' va 'worker' relyatsiyalarini yuklaymiz.
            // Ular uchun alohida Resource'lardan foydalanamiz.
            'service' => new ServiceResource($this->whenLoaded('service')),
            'worker' => new UserResource($this->whenLoaded('worker')),
        ];
    }
}
