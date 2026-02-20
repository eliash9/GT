<?php

namespace Modules\Product\Application\UseCases;

use Illuminate\Support\Str;
use Modules\Product\Application\DTOs\CreateProductDTO;
use Modules\Product\Infrastructure\Models\Product;

class CreateProductUseCase
{
    public function execute(CreateProductDTO $dto): Product
    {
        return Product::create([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'price' => $dto->price,
            'description' => $dto->description,
            'is_active' => $dto->is_active,
        ]);
    }
}
