<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Notifications\SubscriptionSuspended; // <<< IMPORT QILAMIZ

class SubscriptionsCheckOverdue extends Command
{
    /**
     * Konsol buyrug'ining nomi va "imzosi".
     * Biz buni 'php artisan subscriptions:check-overdue' deb chaqiramiz.
     */
    protected $signature = 'subscriptions:check-overdue';

    /**
     * Konsol buyrug'ining tavsifi.
     */
    protected $description = 'Checks for overdue subscriptions and suspends them';

    /**
     * Buyruq logikasini bajarish.
     */
    public function handle()
    {
        $this->info('Checking for overdue subscriptions...');

        $overdueTenants = Tenant::where('subscription_status', 'active')
            ->whereDate('next_billing_date', '<', Carbon::today())
            ->get();

        if ($overdueTenants->isEmpty()) {
            $this->info('No overdue subscriptions found.');
            return 0;
        }

        // FAQAT BITTA SIKL
        foreach ($overdueTenants as $tenant) {
            // 1. Statusni 'suspended' ga o'zgartiramiz
            $tenant->update(['subscription_status' => 'suspended']);

            // 2. Konsolga xabar chiqaramiz
            $this->warn("Tenant '{$tenant->name}' (ID: {$tenant->id}) has been suspended.");

            // 3. XABAR YUBORAMIZ
            $tenant->notify(new SubscriptionSuspended());
        }

        $this->info('Finished checking for overdue subscriptions.');
        return 0;
    }
}
