<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Foydalanuvchi to'lovlar ro'yxatini ko'ra oladimi?
     */
    public function viewAny(User $user): bool
    {
        // Faqat owner, manager yoki cashier ko'ra oladi.
        // Null-safe operatori: agar $user->profile null bo'lsa, xato bermaydi
        return in_array($user->profile?->role, ['owner', 'manager', 'cashier']);
    }

    /**
     * Foydalanuvchi ma'lum bir to'lovni ko'ra oladimi?
     */
    public function view(User $user, Payment $payment): bool
    {
        // User faqat o'zining tenant'iga tegishli to'lovni ko'ra oladi.
        return $user->tenant_id === $payment->tenant_id;
    }

    /**
     * Yangi to'lov yarata oladimi? (Odatda bu OrderController orqali amalga oshiriladi)
     */
    public function create(User $user): bool
    {
        return false; // To'g'ridan-to'g'ri API orqali to'lov yaratishga ruxsat yo'q
    }

    /**
     * To'lov ma'lumotlarini yangilay oladimi?
     */
    public function update(User $user, Payment $payment): bool
    {
        return false; // To'lovlar odatda yangilanmaydi
    }

    /**
     * To'lovni o'chira oladimi?
     */
    public function delete(User $user, Payment $payment): bool
    {
        return false; // To'lovlarni o'chirishga ruxsat yo'q
    }
}