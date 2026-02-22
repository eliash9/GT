<?php

namespace App\Exports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SantriExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Santri::all();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kelas',
            'Kamar',
            'Nama Ayah/Wali',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Nomor HP',
            'Alamat Lengkap',
            'Angkatan',
            'Status Tugas',
            'Kelas Formal',
            'Jabatan Haliyah',
            'Keaktifan Haliyah',
            'Pelanggaran Haliyah',
            'Akademisi',
            'Marhalah Al-Quran',
            'Nilai Ujian Tulis',
            'Nilai Ujian Materi',
            'Nilai Ujian Praktek Kelas',
            'Status Seleksi',
            'Status Kelulusan'
        ];
    }

    public function map($santri): array
    {
        return [
            $santri->nis,
            $santri->nama,
            $santri->jenis_kelamin,
            $santri->kelas,
            $santri->kamar,
            $santri->nama_ayah,
            $santri->tempat_lahir,
            $santri->tanggal_lahir,
            $santri->no_hp,
            $santri->alamat_lengkap,
            $santri->angkatan,
            $santri->status_tugas,
            $santri->kelas_formal,
            $santri->haliyah_jabatan,
            $santri->haliyah_keaktifan,
            $santri->haliyah_pelanggaran,
            $santri->akademisi,
            $santri->marhalah_alquran,
            $santri->nilai_ujian_tulis,
            $santri->nilai_ujian_materi,
            $santri->nilai_ujian_praktek_kelas,
            $santri->status_seleksi,
            $santri->status_kelulusan,
        ];
    }
}
