<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->foreignId('lembaga_id')->constrained('lembagas')->onDelete('cascade');
            $table->string('psm_tahun')->nullable()->comment('contoh: 2025/2026');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            // Skor kecocokan dihitung saat matching: skill wajib x10, diutamakan x5, opsional x2
            $table->unsignedSmallInteger('skor_kecocokan')->default(0);
            $table->enum('status', ['diusulkan', 'disetujui', 'aktif', 'selesai', 'dibatalkan'])->default('diusulkan');
            $table->text('catatan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};
