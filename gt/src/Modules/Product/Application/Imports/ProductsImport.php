<?php

namespace Modules\Product\Application\Imports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Product\Infrastructure\Models\Product;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'slug' => $row['slug'] ?? Str::slug($row['name']),
            'price' => $row['price'] ?? 0,
            'description' => $row['description'] ?? null,
            'is_active' => $row['is_active'] ?? true,
        ]);
    }
}
