<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
            'slug' => $this->slug,
            'phone' => $this->phone,
            'address' => $this->address,
            'subscriptionStatus' => $this->subscription_status, // snake_case'ni camelCase'ga o'girdik
            'monthlyFee' => $this->monthly_fee,
            'nextBillingDate' => $this->next_billing_date,
        ];
    }
}
