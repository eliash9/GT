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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('santri_id')->nullable()->after('reporter_id');
            $table->unsignedBigInteger('pjgt_id')->nullable()->after('santri_id');
            $table->unsignedBigInteger('lembaga_id')->nullable()->after('pjgt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['santri_id', 'pjgt_id', 'lembaga_id']);
        });
    }
};
