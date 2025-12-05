<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // O'ZGARISH: Buyurtma qismi (item) avtomoykaga (tenant) va buyurtmaga (order)
            // "mustahkam" bog'langan. Ota-yozuv o'chsa, bu ham o'chib ketishi kerak.
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Xizmat yoki ishchi o'chsa, bu ustunlar NULL bo'ladi, chunki tarixni saqlash muhim.
            // Bu qismini to'g'ri tanlagansiz, faqat service uchun boshqa yaxshiroq yo'l bor: restrictOnDelete
            // Hozircha bu muhokama qilmaymiz, chunki bu chalkashtirishi mumkin
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();

            // O'ZGARISH: Sxemaga moslab worker_id'ga ->nullable() qo'shildi.
            $table->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete();

            // O'ZGARISH: Loyihadagi boshqa narxlarga moslab integer ishlatildi.
            $table->integer('qty')->default(1);
            $table->integer('unit_price'); // snapshot
            $table->integer('line_total');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
