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
        Schema::table('santris', function (Blueprint $table) {
            $table->enum('status_kelulusan', ['Belum Lulus', 'Lulus', 'Tidak Lulus'])->default('Belum Lulus')->comment('Status Kelulusan Pondok / Diklat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn('status_kelulusan');
        });
    }
};
