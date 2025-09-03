<?php
namespace App\Support\DTO;

use Illuminate\Pagination\LengthAwarePaginator;

final class PageDTO
{
    /**
     * @param array<int, ProductDTO|OrderDTO> $items
     */
    public function __construct(
        public array  $items,
        public int    $page,
        public int    $limit,
        public ?int   $totalItems = null,
        public ?int   $totalPages = null,
    ) {}

    public static function fromPaginator(LengthAwarePaginator $p): self
    {
        return new self(
            items: $p->items(),
            page: $p->currentPage(),
            limit: $p->perPage(),
            totalItems: $p->total(),
            totalPages: $p->lastPage(),
        );
    }
}
