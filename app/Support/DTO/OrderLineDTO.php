<?php

namespace App\Support\DTO;

final class OrderLineDTO
{
    public function __construct(
        public string $productExternalId,
        public string $name,
        public ?string $sku,
        public int $quantity,
        public string $currency,
        public float $unitPrice,
        public float $total,
    ) {}
}
