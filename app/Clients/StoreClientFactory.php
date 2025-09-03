<?php

namespace App\Clients;

use App\Clients\Contracts\IStoreClient;
use App\Clients\Exceptions\UnsupportedPlatformException;
use App\Domain\Enums\Platform;
use App\Models\Store;

final class StoreClientFactory
{
    public function __construct(
        private readonly Shopify\ShopifyClient $shopify,
        private readonly WooCommerce\WooCommerceClient $woo,
    ) {}

    public function for(Store $store): IStoreClient
    {
        return match ($store->platform) {
            Platform::SHOPIFY => $this->shopify->forStore($store),
            Platform::WOOCOMMERCE => $this->woo->forStore($store),
            default => throw new UnsupportedPlatformException("Platform {$store->platform->value} not supported"),
        };
    }
}
