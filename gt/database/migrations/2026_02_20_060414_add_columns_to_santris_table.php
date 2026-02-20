<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('id');
            $table->string('kelas')->nullable()->after('nama');
            $table->string('nama_ayah')->nullable()->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['foto', 'kelas', 'nama_ayah']);
        });
    }
};
