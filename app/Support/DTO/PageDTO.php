<?php

namespace App\Support\DTO;

final class PageDTO
{
    /**
     * @param array<int, ProductDTO|OrderDTO> $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $limit,
        public ?int $totalItems = null,
        public ?int $totalPages = null,
    ) {}
}
