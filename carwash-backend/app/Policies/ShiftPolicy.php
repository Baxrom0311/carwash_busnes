<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Auth\Access\Response;

class ShiftPolicy
{
    /**
     * Foydalanuvchi smenalar ro'yxatini ko'ra oladimi?
     */
    public function viewAny(User $user): bool
    {
        // Faqat owner, manager yoki cashier ko'ra oladi.
        return $user->profile && in_array($user->profile->role, ['owner', 'manager', 'cashier']);
    }

    /**
     * Foydalanuvchi ma'lum bir smenani ko'ra oladimi?
     */
    public function view(User $user, Shift $shift): bool
    {
        // User faqat o'zining tenant'iga tegishli smenani ko'ra oladi.
        return $user->tenant_id === $shift->tenant_id;
    }

    /**
     * Yangi smena yarata oladimi? (Store metodi)
     */
    public function create(User $user): bool
    {
        // Faqat kassir smena ochishi mumkin
        return $user->profile && $user->profile->role === 'cashier';
    }

    /**
     * Smena ma'lumotlarini yangilay oladimi? (Update metodi - smenani yopish)
     */
    public function update(User $user, Shift $shift): bool
    {
        // Faqat smenani ochgan kassir uni yopa oladi va u o'zining tenant'iga tegishli bo'lishi kerak.
        return ($user->id === $shift->user_id && $user->tenant_id === $shift->tenant_id);
    }
}
