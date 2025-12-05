<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Foydalanuvchi buyurtmani ko'ra oladimi?
     */
    public function viewAny(User $user): bool
    {
        // Faqat owner yoki manager ro'yxatni ko'ra oladi.
        // ?-> bu nullsafe operator, agar profile bo'lmasa xato bermaydi.
        return in_array($user->profile?->role, ['owner', 'manager']);
    }
    public function view(User $user, Order $order): bool
    {
        // User faqat o'zining tenant'iga tegishli buyurtmani ko'ra oladi.
        return $user->tenant_id === $order->tenant_id;
    }

    /**
     * Foydalanuvchi yangi buyurtma yarata oladimi?
     */
    public function create(User $user): bool
    {
        // Hozircha tizimga kirgan har qanday xodim yarata oladi (masalan, menejer)
        // Kelajakda rolga qarab cheklash mumkin.
        return true;
    }

    /**
     * Foydalanuvchi buyurtmani yangilay oladimi?
     */
    public function update(User $user, Order $order): bool
    {
        // User faqat o'zining tenant'iga tegishli buyurtmani yangilay oladi.
        return $user->tenant_id === $order->tenant_id;
    }

    /**
     * Foydalanuvchi buyurtmani o'chira oladimi?
     */
    public function delete(User $user, Order $order): bool
    {
        // User faqat o'zining tenant'iga tegishli buyurtmani o'chira oladi.
        return $user->tenant_id === $order->tenant_id;
    }
}
