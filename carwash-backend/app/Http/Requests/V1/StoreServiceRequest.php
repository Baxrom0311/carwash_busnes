<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kelajakda bu yerga rol tekshiruvini qo'shamiz
        return true;
    }

    public function rules(): array
    {
        return [
            // 'tenant_id' endi majburiy emas, biz uni avtomatik olamiz
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'], // Buni qo'shib qo'yamiz
        ];
    }

}
