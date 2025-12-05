<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Bu qator kerak bo'lishi mumkin
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// YUQORI QISMGA IMPORT QILAMIZ
use App\Models\Service;
use App\Models\Shift;
use App\Models\Order; // Qo'shilgan
use App\Models\User; // Qo'shilgan
use App\Models\Payment; // <<< YANGI IMPORT

use App\Policies\ServicePolicy;
use App\Policies\ShiftPolicy;
use App\Policies\UserPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy; // <<< IMPORT

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // MANA SHU YERGA QO'SHAMIZ
        Service::class => ServicePolicy::class,
        Shift::class   => ShiftPolicy::class,
        Order::class   => OrderPolicy::class,
        User::class    => UserPolicy::class,
        Payment::class => PaymentPolicy::class, // <<< QO'SHILDI
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Bu yerga hozircha tegmaymiz
    }
}
