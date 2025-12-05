<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Asosiy tekshiruv Policy'da bo'ladi
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'], // Kelajakda unique qoidasini qo'shamiz
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'], // Parol ixtiyoriy, lekin kiritsa kamida 8 ta belgi
            'role'  => ['required', 'string', Rule::in(['manager', 'cashier', 'worker'])], // Rol majburiy
        ];
    }
}
