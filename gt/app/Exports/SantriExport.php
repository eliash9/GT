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
            'Nama Ayah/Wali',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Nomor HP',
            'Alamat Lengkap',
            'Angkatan',
            'Status Tugas'
        ];
    }

    public function map($santri): array
    {
        return [
            $santri->nis,
            $santri->nama,
            $santri->jenis_kelamin,
            $santri->kelas,
            $santri->nama_ayah,
            $santri->tempat_lahir,
            $santri->tanggal_lahir,
            $santri->no_hp,
            $santri->alamat_lengkap,
            $santri->angkatan,
            $santri->status_tugas,
        ];
    }
}
