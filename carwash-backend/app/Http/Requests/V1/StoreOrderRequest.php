<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id'   => ['required', 'integer', 'exists:tenants,id'],
            'vehicle_id'  => ['nullable', 'integer', 'exists:vehicles,id'],
            'manager_id'  => ['nullable', 'integer', 'exists:users,id'],
            'note'        => ['nullable', 'string'],

            // Bu qism 'items' massivini tekshiradi
            'items'             => ['required', 'array'], // 'items' maydoni majburiy va u massiv bo'lishi shart
            'items.*.service_id'=> ['required', 'integer', 'exists:services,id'], // Massivdagi har bir elementning service_id'si...
            'items.*.worker_id' => ['nullable', 'integer', 'exists:users,id'],   // ... worker_id'si...
            'items.*.qty'       => ['required', 'integer', 'min:1'],              // ... va soni (qty) uchun qoidalar.
        ];
    }
}
