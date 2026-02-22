<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeleksiGTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Skills (Ketrampilan)
        $skills = [
            ['nama' => 'Banjari', 'kategori' => 'seni', 'deskripsi' => 'Kemampuan Banjari'],
            ['nama' => 'Ishari', 'kategori' => 'seni', 'deskripsi' => 'Kemampuan Ishari'],
            ['nama' => 'Quran (Tagoni dan Tartil)', 'kategori' => 'hafalan', 'deskripsi' => 'Membaca Al-Qur\'an dengan Tagoni dan Tartil'],
            ['nama' => 'Khitobah', 'kategori' => 'lainnya', 'deskripsi' => 'Kemampuan Khitobah/Pidato'],
            ['nama' => 'Komputer', 'kategori' => 'teknologi', 'deskripsi' => 'Pengoperasian Komputer dasar']
        ];

        foreach ($skills as $skillData) {
            \App\Models\Skill::firstOrCreate(['nama' => $skillData['nama']], $skillData);
        }

        // 2. Seed Dummy Santri untuk simulasi penentuan Lolos Tahap Awal
        $faker = \Faker\Factory::create('id_ID');

        // Pastikan kita membuat beberapa data dummy
        for ($i = 1; $i <= 10; $i++) {
            $santri = \App\Models\Santri::create([
                'nis' => 'SNM' . date('Y') . rand(10000, 99999),
                'nama' => $faker->name,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                'alamat_lengkap' => $faker->address,
                'no_hp' => $faker->phoneNumber,
                'angkatan' => date('Y'),
                'status_tugas' => 'Menunggu',
                'kelas' => $faker->randomElement(['XI', 'XII', 'Lulus']),
                'kamar' => $faker->randomElement(['A-01', 'A-02', 'B-01', 'C-12']),
                'kelas_formal' => $faker->randomElement(['Lulus SMA', 'Kelas XI', 'Kelas XII']),
                // Data Haliyah
                'haliyah_jabatan' => $faker->randomElement(['Ketua Kamar', 'Pengurus Pondok', 'Santri Biasa', 'Keamanan']),
                'haliyah_keaktifan' => $faker->randomElement(['A', 'B', 'C']),
                'haliyah_pelanggaran' => $faker->randomElement(['Tidak Ada', 'Terlambat Ngaji', 'Tidak sholat jamaah']),
                // Akademisi & Quran
                'akademisi' => $faker->randomElement(['A', 'B', 'C']),
                'marhalah_alquran' => $faker->randomElement(['A', 'B', 'C']),
                // Ujian Praktek
                'nilai_ujian_tulis' => $faker->numberBetween(60, 100),
                'nilai_ujian_materi' => $faker->numberBetween(60, 100),
                'nilai_ujian_praktek_kelas' => $faker->numberBetween(60, 100),
                // Status Seleksi
                'status_seleksi' => 'Belum Diproses', // Default awal
                'status_kelulusan' => $faker->randomElement(['Belum Lulus', 'Lulus', 'Tidak Lulus'])
            ]);

            // Assign random skill(s) ke santri
            $randomSkills = \App\Models\Skill::inRandomOrder()->limit(rand(1, 3))->pluck('id')->toArray();
            
            // skill pivot default butuh level or keterangan - santri_skill structure might require handling it.
            // based on Santri.php : withPivot(['level', 'keterangan'])
            foreach ($randomSkills as $skillId) {
                $santri->skills()->attach($skillId, [
                    'level' => 'dasar', // as default assumption
                    'keterangan' => 'Dinilai saat pendaftaran'
                ]);
            }
        }
    }
}
