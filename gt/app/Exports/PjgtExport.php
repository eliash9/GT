<?php

namespace App\Exports;

use App\Models\Pjgt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PjgtExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pjgt::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'Nomor HP'
        ];
    }

    public function map($pjgt): array
    {
        return [
            $pjgt->id,
            $pjgt->nama,
            $pjgt->no_hp,
        ];
    }
}
