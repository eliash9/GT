<?php

namespace Modules\Product\Application\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Product\Infrastructure\Models\Product;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::select('id', 'name', 'slug', 'price', 'description', 'is_active', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Price',
            'Description',
            'Is Active',
            'Created At',
        ];
    }
}
