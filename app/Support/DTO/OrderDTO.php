<?php

namespace App\Support\DTO;

final class OrderDTO
{
    public function __construct(
        public string $externalId,
        public string $number,
        public string $status,
        public string $customerName,
        public string $customerEmail,
        public \DateTimeInterface $createdAt,
        public array $lines,
        public string $currency,
        public float $total,
    ) {}
}
