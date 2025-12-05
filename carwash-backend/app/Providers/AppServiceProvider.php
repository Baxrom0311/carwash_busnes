<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; // <<< 1. EVENT FASADINI IMPORT QILAMIZ

// 2. BIZNING EVENT VA LISTENER'NI IMPORT QILAMIZ
use App\Events\OrderUpdated;
use App\Listeners\CalculateWagesListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 3. MANA SHU YERGA BOG'LIQLIKNI YOZAMIZ
        // Ma'nosi: "Har safar 'OrderUpdated' hodisasi yuz berganda,
        // 'CalculateWagesListener' klassini ishga tushir".
        Event::listen(
            OrderUpdated::class,
            CalculateWagesListener::class
        );
    }
}
