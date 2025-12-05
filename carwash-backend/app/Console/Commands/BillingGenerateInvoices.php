<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class BillingGenerateInvoices extends Command
{
    protected $signature = 'billing:generate-invoices';
    protected $description = 'Generates new monthly invoices for active tenants';

    public function handle()
    {
        $this->info('Generating upcoming invoices...');

        // Keyingi to'lov sanasiga 7 kun qolgan (yoki o'tib ketgan)
        // va statusi 'active' bo'lgan barcha tenant'larni topamiz.
        $tenantsToBill = Tenant::where('subscription_status', 'active')
            ->whereDate('next_billing_date', '<=', now()->addDays(7))
            ->get();

        if ($tenantsToBill->isEmpty()) {
            $this->info('No tenants to bill today.');
            return 0;
        }

        foreach ($tenantsToBill as $tenant) {
            // Keyingi oyning boshini hisoblaymiz
            $nextBillingMonth = Carbon::parse($tenant->next_billing_date)->startOfMonth();

            // Bu tenant uchun KEYINGI OYGA allaqachon invoice yaratilganmi?
            $invoiceExists = TenantInvoice::where('tenant_id', $tenant->id)
                ->whereDate('period_month', $nextBillingMonth)
                ->exists();

            if ($invoiceExists) {
                $this->line("Invoice for '{$tenant->name}' for next month already exists. Skipping.");
                continue;
            }

            // Yangi hisob-faktura yaratamiz
            TenantInvoice::create([
                'tenant_id' => $tenant->id,
                'period_month' => $nextBillingMonth,
                'amount' => $tenant->monthly_fee,
                'status' => 'pending',
                'due_at' => $tenant->next_billing_date, // To'lov muddati - aynan o'sha sana
            ]);

            $this->info("Invoice created for '{$tenant->name}' for the period of " . $nextBillingMonth->format('F Y'));
        }

        $this->info('Finished generating invoices.');
        return 0;
    }
}
