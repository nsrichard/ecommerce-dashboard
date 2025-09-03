<?php

namespace App\Services;

use App\Models\Store;
use DateTimeImmutable;

class MetricService
{
    public function summary(
        Store $store,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): array {
        $from ??= new DateTimeImmutable('now -30 days');
        $to   ??= new DateTimeImmutable('now');

        $ordersPage = app(OrderService::class)
            ->listRecent($store, $from, $to, ['page'=>1,'limit'=>1000]);
        $orderCount = count($ordersPage->items);
        $salesTotal = array_reduce(
            $ordersPage->items,
            fn($carry, $order) => $carry + $order->total,
            0.0
        );

        $productsPage = app(ProductService::class)
            ->list($store, [], ['page'=>1,'limit'=>1000]);
        $productCount = count($productsPage->items);

        return [
            'orderCount'   => $orderCount,
            'salesTotal'   => $salesTotal,
            'productCount' => $productCount,
        ];
    }
}
