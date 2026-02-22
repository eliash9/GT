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
        Schema::table('penugasan', function (Blueprint $table) {
            $table->string('kode_tugas')->unique()->nullable()->after('id')->comment('Nomor Kode Tugas Unik');
            $table->date('tanggal_mulai')->nullable()->after('catatan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropColumn(['kode_tugas', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
