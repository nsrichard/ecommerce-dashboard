<?php

namespace App\Clients\Contracts;

use App\Support\DTO\PageDTO;

interface IStoreClient
{
    public function getProducts(array $filters = [], array $pagination = ['page' => 1, 'limit' => 50]): PageDTO;
    public function getOrders(\DateTimeInterface $from, \DateTimeInterface $to, array $pagination = ['page' => 1, 'limit' => 50]): PageDTO;
    public function healthCheck(): bool;
}
