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
            $table->string('haliyah_jabatan')->nullable()->comment('Jabatan saat di pondok');
            $table->enum('haliyah_keaktifan', ['A', 'B', 'C'])->nullable()->comment('Keaktifan santri');
            $table->text('haliyah_pelanggaran')->nullable()->comment('Riwayat pelanggaran');
            $table->enum('akademisi', ['A', 'B', 'C'])->nullable()->comment('Nilai Akademisi');
            $table->string('kelas_formal')->nullable()->comment('Kelas formal, misal: lulus SMA/Kelas x');
            $table->integer('nilai_ujian_tulis')->nullable()->comment('Nilai ujian tulis 0-100');
            $table->integer('nilai_ujian_materi')->nullable()->comment('Nilai ujian materi 0-100');
            $table->integer('nilai_ujian_praktek_kelas')->nullable()->comment('Nilai ujian penilaian kelas 0-100');
            $table->enum('marhalah_alquran', ['A', 'B', 'C'])->nullable()->comment('Tingkat baca quran');
            $table->enum('status_seleksi', ['Belum Diproses', 'Lolos Tahap Awal', 'Lolos Tahap Akhir', 'Tidak Lolos'])->default('Belum Diproses')->comment('Status seleksi GT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn([
                'haliyah_jabatan',
                'haliyah_keaktifan',
                'haliyah_pelanggaran',
                'akademisi',
                'kelas_formal',
                'nilai_ujian_tulis',
                'nilai_ujian_materi',
                'nilai_ujian_praktek_kelas',
                'marhalah_alquran',
                'status_seleksi'
            ]);
        });
    }
};
