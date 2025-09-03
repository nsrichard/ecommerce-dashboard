<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Store;
use App\Models\Product;
use App\Services\SyncService;
use PHPUnit\Framework\Attributes\Test;

class ProductsControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();

        // Stub de sincronización real
        $this->mock(SyncService::class, function ($mock) {
            $mock->shouldReceive('syncProducts')
                 ->with($this->store)
                 ->andReturnNull()
                 ->once();
        });
    }

    #[Test]
    public function it_filters_by_min_price_and_paginates_results(): void
    {
        Product::factory()->create([
            'store_id'    => $this->store->id,
            'external_id' => '1',
            'name'        => 'Low Price',
            'sku'         => 'LOW-1',
            'price'       => 50.00,
            'currency'    => 'USD',
        ]);
        Product::factory()->create([
            'store_id'    => $this->store->id,
            'external_id' => '2',
            'name'        => 'High Price',
            'sku'         => 'HIGH-1',
            'price'       => 150.00,
            'currency'    => 'USD',
        ]);

        $response = $this->get(route('products.index', [
            'store'     => $this->store->id,
            'min_price' => 100,
            'limit'     => 1,
        ]));

        $response->assertStatus(200);

        $products = $response->viewData('products');
        $this->assertEquals(1, $products->count());
        $this->assertEquals('High Price', $products->first()->name);
    }

    #[Test]
    public function it_searches_by_name_or_sku(): void
    {
        Product::factory()->create([
            'store_id'    => $this->store->id,
            'external_id' => '3',
            'name'        => 'Apple iPhone',
            'sku'         => 'IPHN-1234',
            'price'       => 999.99,
            'currency'    => 'USD',
        ]);
        Product::factory()->create([
            'store_id'    => $this->store->id,
            'external_id' => '4',
            'name'        => 'Samsung Galaxy',
            'sku'         => 'SMSG-5678',
            'price'       => 899.99,
            'currency'    => 'USD',
        ]);

        $respName = $this->get(route('products.index', [
            'store' => $this->store->id,
            'q'     => 'Apple',
        ]));
        $respName->assertStatus(200);
        $this->assertEquals(1, $respName->viewData('products')->count());
        $this->assertStringContainsString('Apple iPhone', $respName->getContent());

        $respSku = $this->get(route('products.index', [
            'store' => $this->store->id,
            'q'     => 'SMSG',
        ]));
        $respSku->assertStatus(200);
        $this->assertEquals(1, $respSku->viewData('products')->count());
        $this->assertStringContainsString('Samsung Galaxy', $respSku->getContent());
    }

    #[Test]
    public function it_returns_correct_page_two_of_results(): void
    {
        Product::factory()->count(3)->create([
            'store_id'    => $this->store->id,
            'external_id' => fn() => (string) rand(100, 999),
            'price'       => 10.00,
            'currency'    => 'USD',
        ]);

        $respPage1 = $this->get(route('products.index', [
            'store' => $this->store->id,
            'limit' => 2,
            'page'  => 1,
        ]));
        $respPage1->assertStatus(200);
        $this->assertCount(2, $respPage1->viewData('products'));

        $respPage2 = $this->get(route('products.index', [
            'store' => $this->store->id,
            'limit' => 2,
            'page'  => 2,
        ]));
        $respPage2->assertStatus(200);
        $this->assertCount(1, $respPage2->viewData('products'));
    }
}
