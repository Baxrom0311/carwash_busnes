<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Foydalanuvchilar ro'yxatini ko'rish huquqi.
     */
    public function viewAny(User $user): bool
    {
        // Faqat owner, manager yoki cashier ko'ra oladi.
        return $user->profile && in_array($user->profile->role, ['owner', 'manager', 'cashier']);
        // return true; // <<< VAQTINCHA O'ZGARISH
    }

    /**
     * Yangi avtomobil yaratish huquqi.
     */
    public function create(User $user): bool
    {
        return $user->profile && in_array($user->profile->role, ['owner', 'manager', 'cashier']);
    }

    /**
     * Avtomobil ma'lumotlarini yangilash huquqi.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        // User faqat o'zining tenant'idagi avtomobilni yangilay oladi.
        return ($user->profile && in_array($user->profile->role, ['owner', 'manager'])) && $user->tenant_id === $vehicle->tenant_id;
    }

    /**
     * Avtomobilni o'chirish huquqi.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        // User faqat o'zining tenant'idagi avtomobilni o'chira oladi.
        return ($user->profile && in_array($user->profile->role, ['owner', 'manager'])) &&
            $user->tenant_id === $vehicle->tenant_id;
    }
}