<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNewUserAction
{
    use AsAction;

    public function handle(array $data, int $tenantId): User
    {
        // Bu Action faqat bitta ishni qiladi: ma'lumotlarni olib,
        // yangi User va uning Profile'ini yaratib, tayyor User modelini qaytaradi.

        return DB::transaction(function () use ($data, $tenantId) {
            $user = User::create([
                'tenant_id' => $tenantId,
                'name'      => $data['name'],
                'phone'     => $data['phone'],
                'email'     => $data['email'] ?? null,
                'password'  => isset($data['password']) ? Hash::make($data['password']) : null,
            ]);

            $user->profile()->create([
                'role' => $data['role'],
            ]);

            return $user;
        });
    }
}
