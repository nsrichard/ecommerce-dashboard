<?php

namespace App\Domain\Enums;

enum ExportType: string
{
    case PRODUCTS = 'products';
    case ORDERS = 'orders';
}
