<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Foydalanuvchilar ro'yxatini ko'rish huquqi.
     */
    public function viewAny(User $user): bool
    {
        // Faqat owner yoki manager ko'ra oladi.
        return $user->profile && in_array($user->profile->role, ['owner', 'manager']);
    }

    /**
     * Boshqa bir foydalanuvchi ma'lumotini ko'rish huquqi.
     */
    public function view(User $user, User $model): bool
    {
        // Owner/manager faqat o'zining tenant'idagi userlarni ko'ra oladi.
        return in_array($user->profile?->role, ['owner', 'manager']) &&
            $user->tenant_id === $model->tenant_id;
    }

    /**
     * Yangi foydalanuvchi yaratish huquqi.
     */
    public function create(User $user): bool
    {
        // Faqat owner yoki manager yangi xodim qo'sha oladi.
        return in_array($user->profile?->role, ['owner', 'manager']);
    }

    /**
     * Foydalanuvchi ma'lumotlarini yangilash huquqi.
     */
    public function update(User $user, User $model): bool
    {
        // Owner/manager faqat o'zining tenant'idagi userlarni yangilay oladi.
        return in_array($user->profile?->role, ['owner', 'manager']) &&
            $user->tenant_id === $model->tenant_id;
    }

    /**
     * Foydalanuvchini o'chirish huquqi.
     */
    public function delete(User $user, User $model): bool
    {
        // Owner/manager faqat o'zining tenant'idagi userlarni o'chira oladi.
        return in_array($user->profile?->role, ['owner', 'manager']) &&
            $user->tenant_id === $model->tenant_id;
    }
}
