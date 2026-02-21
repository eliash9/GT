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
            $table->dropColumn(['psm_tahun', 'tanggal_mulai', 'tanggal_selesai']);
            $table->foreignId('tahun_psm_id')->nullable()->after('lembaga_id')->constrained('tahun_psms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropForeign(['tahun_psm_id']);
            $table->dropColumn('tahun_psm_id');
            $table->string('psm_tahun')->nullable()->after('lembaga_id');
            $table->date('tanggal_mulai')->nullable()->after('psm_tahun');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }
};
