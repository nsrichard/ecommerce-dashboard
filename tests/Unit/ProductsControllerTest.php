<?php

use Tests\TestCase;
use App\Models\Store;
use App\Models\Product;
use App\Services\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    

    public function test_index_calls_sync_and_returns_products()
    {
        $store = Store::factory()->create();
        Product::factory()->count(3)->create(['store_id' => $store->id]);

        $sync = Mockery::mock(SyncService::class);
        $sync->shouldReceive('syncProducts')->with($store)->once();
        $this->app->instance(SyncService::class, $sync);

        $response = $this->get(route('stores.products.index', $store));

        $response->assertStatus(200);
        $response->assertViewHas('products', function($products) {
            return $products->count() === 3;
        });
    }
}
