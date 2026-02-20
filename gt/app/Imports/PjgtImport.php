<?php

namespace App\Imports;

use App\Models\Pjgt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PjgtImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pjgt([
            'nama' => $row['nama_lengkap'] ?? null,
            'no_hp' => $row['nomor_hp'] ?? null,
        ]);
    }
}
