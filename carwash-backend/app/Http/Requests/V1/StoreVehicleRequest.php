<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hozircha huquqlarni tekshirmaymiz
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tenant_id'   => ['required', 'integer', 'exists:tenants,id'],
            'plate_number'=> ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'model'       => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'max:255'],
            'owner_name'  => ['nullable', 'string', 'max:255'],
            'owner_phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
