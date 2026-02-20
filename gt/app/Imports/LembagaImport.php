<?php

namespace App\Imports;

use App\Models\Lembaga;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LembagaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Lembaga([
            'nama' => $row['nama_lembaga'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'urlmap' => $row['url_map'] ?? null,
            'status' => $row['status'] ?? 'aktif',
            'wilayah_id' => $row['id_wilayah'] ?? null,
            'pjgt_id' => $row['id_pjgt'] ?? null,
        ]);
    }
}
