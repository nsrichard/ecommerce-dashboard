<?php

namespace App\Services;

use App\Clients\StoreClientFactory;
use App\Models\Store;
use App\Support\DTO\PageDTO;
use DateInterval;
use DateTimeImmutable;

final class OrderService
{
    public function __construct(private readonly StoreClientFactory $factory) {}

    public function listRecent(Store $store, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null, array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $to ??= new DateTimeImmutable('now');
        $from ??= (new DateTimeImmutable('now'))->sub(new DateInterval('P30D'));

        $client = $this->factory->for($store);
        return $client->getOrders($from, $to, $pagination);
    }
}
