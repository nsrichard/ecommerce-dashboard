<?php

namespace App\Domain\Enums;

enum Platform: string
{
    case SHOPIFY = 'shopify';
    case WOOCOMMERCE = 'woocommerce';
}
