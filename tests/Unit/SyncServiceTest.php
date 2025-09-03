<?php

namespace Tests\Unit\Services;

use App\Clients\StoreClientFactory;
use App\Models\Store;
use App\Services\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class SyncServiceTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        
        config([
            'logging.channels.sync' => [
                'driver'  => 'monolog',
                'handler' => \Monolog\Handler\NullHandler::class,
            ],
        ]);
    }

    public function test_sync_products_successful_inserts_products_and_updates_store(): void
    {

        // 1) Preparamos la tienda
        $store = Store::factory()->create([
            'name'     => 'Tienda Sync',
            'platform' => 'woocommerce',
            'domain'   => 'sync.test',
        ]);

        // 2) DTO + página simulada
        $dto  = (object)[
            'externalId' => 'p1',
            'name'       => 'Producto Uno',
            'sku'        => 'SKU1',
            'price'      => 9.99,
            'currency'   => 'USD',
            'imageUrl'   => 'http://img.test/1.png',
        ];
        $page = (object)['items' => [$dto]];

        // 3) Cliente stub que siempre devuelve esa página
        $stubbedClient = new class($page) {
            public function __construct(private object $page) {}
            public function getProducts(array $_, array $__): object
            {
                return $this->page;
            }
        };

        // 4) Creamos un alias-mock de StoreClientFactory
        $factoryMock = Mockery::mock('alias:' . StoreClientFactory::class);
        $factoryMock
            ->shouldReceive('for')
            ->once()
            ->with($store)
            ->andReturn($stubbedClient);

        // 5) Inyectamos el mock y ejecutamos
        $service = new SyncService($factoryMock);
        $service->syncProducts($store);

        // 6) Aserciones
        $this->assertDatabaseHas('products', [
            'store_id'    => $store->id,
            'external_id' => 'p1',
            'name'        => 'Producto Uno',
            'sku'         => 'SKU1',
            'price'       => 9.99,
            'currency'    => 'USD',
            'image_url'   => 'http://img.test/1.png',
            'stock'       => 0,
        ]);

        $this->assertDatabaseHas('stores', [
            'id'               => $store->id,
            'last_sync_status' => 'success',
        ]);
    }

    public function test_sync_products_handles_empty_response_and_sets_failed_status(): void
    {

        $store = Store::factory()->create([
            'name'     => 'Tienda Vacía',
            'platform' => 'woocommerce',
            'domain'   => 'empty.test',
        ]);

        $stubbedClient = new class {
            public function getProducts(array $_, array $__): object
            {
                return (object)['items' => []];
            }
        };

        $factoryMock = Mockery::mock('alias:' . StoreClientFactory::class);
        $factoryMock
            ->shouldReceive('for')
            ->once()
            ->with($store)
            ->andReturn($stubbedClient);

        (new SyncService($factoryMock))->syncProducts($store);

        $this->assertDatabaseHas('stores', [
            'id'               => $store->id,
            'last_sync_status' => 'failed',
        ]);
    }

    public function test_sync_products_logs_exception_and_sets_failed_status(): void
    {

        $store = Store::factory()->create([
            'name'     => 'Tienda Error',
            'platform' => 'woocommerce',
            'domain'   => 'error.test',
        ]);

        $stubbedClient = new class {
            public function getProducts(array $_, array $__): object
            {
                throw new \RuntimeException('Simulated failure');
            }
        };

        $factoryMock = Mockery::mock('alias:' . StoreClientFactory::class);
        $factoryMock
            ->shouldReceive('for')
            ->once()
            ->with($store)
            ->andReturn($stubbedClient);

        (new SyncService($factoryMock))->syncProducts($store);

        Log::shouldHaveReceived('error')->once();

        $this->assertDatabaseHas('stores', [
            'id'               => $store->id,
            'last_sync_status' => 'failed',
        ]);
    }
}
