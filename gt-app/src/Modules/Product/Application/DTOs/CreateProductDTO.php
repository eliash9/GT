<?php

namespace Modules\Product\Application\DTOs;

class CreateProductDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public string $description,
        public bool $is_active = true,
    ) {
    }
}
