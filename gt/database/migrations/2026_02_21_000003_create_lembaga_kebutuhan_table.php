<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembaga_kebutuhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembaga_id')->constrained('lembagas')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skill')->onDelete('cascade');
            $table->enum('prioritas', ['wajib', 'diutamakan', 'opsional'])->default('diutamakan');
            $table->unsignedSmallInteger('kuota')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['lembaga_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembaga_kebutuhan');
    }
};
