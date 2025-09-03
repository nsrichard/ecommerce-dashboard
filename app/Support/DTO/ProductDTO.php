<?php

namespace App\Support\DTO;

final class ProductDTO
{
    public function __construct(
        public string $externalId,
        public string $name,
        public ?string $sku,
        public string $currency,
        public float $price,
        public ?string $imageUrl,
    ) {}
}
