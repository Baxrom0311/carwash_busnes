<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Tenant;
use App\Models\TenantInvoice; // <<< YANGI IMPORT
use App\Models\User;
use App\Models\WageRule;      // <<< YANGI IMPORT
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // =================================================================
        // 1-chi AVTOMOYKA (ANHOR) UCHUN MA'LUMOTLAR
        // =================================================================
        $tenant1 = Tenant::create(['name' => 'Anhor Avto Moyka', 'slug' => 'anhor']);

        // --- Foydalanuvchilar ---
        $manager1 = User::create(['tenant_id' => $tenant1->id, 'name' => 'Anhor Menejeri', 'phone' => '998901111111']);
        $manager1->profile()->create(['role' => 'manager']);

        $worker1 = User::create(['tenant_id' => $tenant1->id, 'name' => 'Anhor Ishchisi', 'phone' => '998901111112']);
        $worker1->profile()->create(['role' => 'worker']);

        // --- Xizmatlar ---
        $service1_1 = Service::create(['tenant_id' => $tenant1->id, 'name' => 'Anhor Tozalash', 'price' => 50000]);
        $service1_2 = Service::create(['tenant_id' => $tenant1->id, 'name' => 'Anhor Polirovka', 'price' => 150000]);

        // --- Ish haqi qoidalari ---
        WageRule::create(['tenant_id' => $tenant1->id, 'service_id' => $service1_1->id, 'rule_type' => 'percent', 'value' => 20]);
        WageRule::create(['tenant_id' => $tenant1->id, 'service_id' => $service1_2->id, 'rule_type' => 'fixed', 'value' => 30000]);

        // --- TO'LANMAGAN HISOB-FAKTURA (PAYME UCHUN) ---
        TenantInvoice::create([
            'tenant_id' => $tenant1->id,
            'period_month' => now()->startOfMonth(),
            'amount' => 120000,
            'status' => 'pending',
            'due_at' => now()->addDays(5),
        ]);


        // =================================================================
        // 2-chi AVTOMOYKA (BODOMZOR) UCHUN MA'LUMOTLAR
        // =================================================================
        $tenant2 = Tenant::create(['name' => 'Bodomzor Car Wash', 'slug' => 'bodomzor']);

        // --- Foydalanuvchilar ---
        $manager2 = User::create(['tenant_id' => $tenant2->id, 'name' => 'Bodomzor Menejeri', 'phone' => '998902222222']);
        $manager2->profile()->create(['role' => 'manager']);

        // --- Xizmatlar ---
        Service::create(['tenant_id' => $tenant2->id, 'name' => 'Bodomzor Himchistka', 'price' => 300000]);
    }
}
