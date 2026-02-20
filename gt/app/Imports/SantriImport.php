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
            'nama_ayah' => $row['nama_ayahwali'] ?? $row['nama_ayah_wali'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? 'Bangkalan',
            'tanggal_lahir' => $tanggalLahir,
            'no_hp' => $row['nomor_hp'] ?? null,
            'alamat_lengkap' => $row['alamat_lengkap'] ?? null,
            'angkatan' => isset($row['angkatan']) ? (int) $row['angkatan'] : date('Y'),
            'status_tugas' => $row['status_tugas'] ?? 'Menunggu',
        ]);
    }
}
