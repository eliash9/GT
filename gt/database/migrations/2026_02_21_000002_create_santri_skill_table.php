<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skill')->onDelete('cascade');
            $table->enum('level', ['dasar', 'menengah', 'mahir'])->default('dasar');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_skill');
    }
};
