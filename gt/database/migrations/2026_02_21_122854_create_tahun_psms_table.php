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
        Schema::create('tahun_psms', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->date('tanggal_mulai_masehi')->nullable();
            $table->date('tanggal_selesai_masehi')->nullable();
            $table->string('tanggal_mulai_hijriah')->nullable();
            $table->string('tanggal_selesai_hijriah')->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_psms');
    }
};
