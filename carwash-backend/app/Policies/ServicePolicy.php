<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    /**
     * Barcha xizmatlar ro'yxatini ko'rish huquqini belgilaydi.
     * Hozircha barcha tizimga kirgan userlar ko'ra oladi.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Bitta aniq xizmatni ko'rish huquqini belgilaydi.
     */
    public function view(User $user, Service $service): bool
    {
        // User faqat o'zining tenant'iga tegishli service'ni ko'ra oladi.
        return $user->tenant_id === $service->tenant_id;
    }

    /**
     * Yangi xizmat yaratish huquqini belgilaydi.
     */
    public function create(User $user): bool
    {
        // Kelajakda bu yerga rol tekshiruvi qo'shiladi:
        // return $user->profile->role === 'owner' || $user->profile->role === 'manager';
        return $user->profile && in_array($user->profile->role, ['owner', 'manager']);
    }

    /**
     * Mavjud xizmatni yangilash huquqini belgilaydi.
     */
    public function update(User $user, Service $service): bool
    {
        // User faqat o'zining tenant'iga tegishli service'ni yangilay oladi.
        return $user->tenant_id === $service->tenant_id;
    }

    /**
     * Mavjud xizmatni o'chirish huquqini belgilaydi.
     */
    public function delete(User $user, Service $service): bool
    {
        // User faqat o'zining tenant'iga tegishli service'ni o'chira oladi.
        return $user->tenant_id === $service->tenant_id;
    }

    // ... forceDelete va restore metodlari (soft delete uchun) ...
}
