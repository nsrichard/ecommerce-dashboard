<?php

namespace Database\Seeders;

use App\Domain\Enums\Platform;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::query()->firstOrCreate(
            ['platform' => Platform::SHOPIFY->value, 'domain' => 'amplifica-demo.myshopify.com'],
            ['name' => 'Amplifica Shopify', 'status' => 'disconnected']
        );

        Store::query()->firstOrCreate(
            ['platform' => Platform::WOOCOMMERCE->value, 'domain' => 'tienda-demo.amplifica.com'],
            ['name' => 'Amplifica Woo', 'status' => 'disconnected']
        );

    }
}
