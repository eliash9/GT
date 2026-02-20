<?php

namespace App\Exports;

use App\Models\Lembaga;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LembagaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Lembaga::with(['wilayah', 'pjgt'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Lembaga',
            'Alamat',
            'Latitude',
            'Longitude',
            'URL Map',
            'Status',
            'ID Wilayah',
            'ID PJGT'
        ];
    }

    public function map($lembaga): array
    {
        return [
            $lembaga->id,
            $lembaga->nama,
            $lembaga->alamat,
            $lembaga->latitude,
            $lembaga->longitude,
            $lembaga->urlmap,
            $lembaga->status,
            $lembaga->wilayah_id,
            $lembaga->pjgt_id,
        ];
    }
}
