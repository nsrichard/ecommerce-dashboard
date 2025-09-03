<?php

namespace App\Services;

use App\Clients\StoreClientFactory;
use App\Models\Store;
use App\Support\DTO\PageDTO;

final class ProductService
{
    public function __construct(private readonly StoreClientFactory $factory) {}

    public function list(Store $store, array $filters = [], array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $client = $this->factory->for($store);
        return $client->getProducts($filters, $pagination);
    }
}
