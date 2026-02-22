<?php

namespace App\Imports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $tanggalLahir = now()->format('Y-m-d');
        if (!empty($row['tanggal_lahir'])) {
            if (is_numeric($row['tanggal_lahir'])) {
                try {
                    $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                }
            } else {
                try {
                    $tanggalLahir = \Carbon\Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                }
            }
        }

        return new Santri([
            'nis' => $row['nis'],
            'nama' => $row['nama_lengkap'] ?? null,
            'jenis_kelamin' => isset($row['jenis_kelamin']) ? strtoupper(substr($row['jenis_kelamin'], 0, 1)) : 'L',
            'kelas' => $row['kelas'] ?? null,
            'kamar' => $row['kamar'] ?? null,
            'nama_ayah' => $row['nama_ayahwali'] ?? $row['nama_ayah_wali'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? 'Bangkalan',
            'tanggal_lahir' => $tanggalLahir,
            'no_hp' => $row['nomor_hp'] ?? null,
            'alamat_lengkap' => $row['alamat_lengkap'] ?? null,
            'angkatan' => isset($row['angkatan']) ? (int) $row['angkatan'] : date('Y'),
            'status_tugas' => $row['status_tugas'] ?? 'Menunggu',
            
            // Evaluasi & Seleksi
            'kelas_formal' => $row['kelas_formal'] ?? null,
            'haliyah_jabatan' => $row['jabatan_haliyah'] ?? $row['haliyah_jabatan'] ?? null,
            'haliyah_keaktifan' => $row['keaktifan_haliyah'] ?? $row['haliyah_keaktifan'] ?? null,
            'haliyah_pelanggaran' => $row['pelanggaran_haliyah'] ?? $row['haliyah_pelanggaran'] ?? null,
            'akademisi' => $row['akademisi'] ?? null,
            'marhalah_alquran' => $row['marhalah_al_quran'] ?? $row['marhalah_alquran'] ?? null,
            'nilai_ujian_tulis' => $row['nilai_ujian_tulis'] ?? null,
            'nilai_ujian_materi' => $row['nilai_ujian_materi'] ?? null,
            'nilai_ujian_praktek_kelas' => $row['nilai_ujian_praktek_kelas'] ?? null,
            'status_seleksi' => $row['status_seleksi'] ?? 'Belum Diproses',
            'status_kelulusan' => $row['status_kelulusan'] ?? 'Belum Lulus',
        ]);
    }
}
