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
        Schema::table('staff_profiles', function (Blueprint $table) {
            // 'role' ustunidan keyin yangi 'avatar_path' ustunini qo'shamiz
            $table->string('avatar_path')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            // Migratsiyani bekor qilganda, shu ustunni o'chirib tashlaymiz
            $table->dropColumn('avatar_path');
        });
    }
};
