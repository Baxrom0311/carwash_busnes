<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hozircha huquqlarni tekshirmaymiz
        return true;
    }

    public function rules(): array
    {
        return [
            // Biz asosan 'status'ni o'zgartirishga ruxsat beramiz.
            'status' => [
                'sometimes', // Bu maydon har doim ham yuborilmasligi mumkin
                'string',
                // Qiymat faqat shu ro'yxatdagilardan biri bo'lishi mumkin:
                Rule::in(['new', 'in_progress', 'done', 'paid', 'canceled']),
            ],
            // Kelajakda bu yerga 'manager_id', 'cashier_id' kabi
            // boshqa maydonlarni yangilash qoidalarini ham qo'shish mumkin.
        ];
    }
}
