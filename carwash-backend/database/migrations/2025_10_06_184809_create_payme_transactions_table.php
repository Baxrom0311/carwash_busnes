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
        Schema::create('payme_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('tenant_invoice_id')->constrained('tenant_invoices')->cascadeOnDelete();
            $table->string('paycom_id')->unique();
            $table->bigInteger('paycom_time')->nullable();
            $table->smallInteger('state')->default(1);
            $table->bigInteger('amount');
            $table->char('currency', 3)->default('UZS');
            $table->bigInteger('perform_time')->nullable();
            $table->bigInteger('cancel_time')->nullable();
            $table->smallInteger('cancel_reason')->nullable();
            $table->index(['tenant_id', 'tenant_invoice_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payme_transactions');
    }
};
