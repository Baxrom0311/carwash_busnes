<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\OrderResource;
class PaymentResource extends JsonResource
{
    /**
     * Resursni massivga o'girish.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'amount'    => $this->amount,
            'method'    => $this->method,
            'paidAt'    => $this->paid_at?->toISOString(),   // yaxshiroq format
            'ref'       => $this->ref,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),

            // Bog‘liq orderni faqat yuklangan bo‘lsa qaytaradi
            'order'     => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
