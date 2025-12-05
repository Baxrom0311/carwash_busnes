<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Bu yerda biz o'zimiz yaratgan buyruqlarni ro'yxatdan o'tkazishimiz mumkin,
        // lekin Laravel 11+ da bu avtomatik amalga oshiriladi.
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // 1. Muddati o'tgan obunalarni tekshirish (har kuni soat 00:01 da)
        $schedule->command('subscriptions:check-overdue')->dailyAt('00:01');

        // 2. Yaqinlashayotgan to'lovlar uchun hisob-faktura yaratish (har kuni soat 00:05 da)
        $schedule->command('billing:generate-invoices')->dailyAt('00:05');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        // Bu metod Laravel 11+ da buyruqlarni avtomatik topish uchun ishlatiladi.
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
