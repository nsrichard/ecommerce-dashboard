<?php
namespace App\Services;

use App\Clients\StoreClientFactory;
use App\Domain\Enums\ExportStatus;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncService
{
    public function __construct(private StoreClientFactory $factory) {}

    public function syncProducts(Store $store): void
    {
        Log::channel('sync')->info('sync.products.start', ['store_id' => $store->id]);
        try {
            $client = $this->factory->for($store);
            $page   = $client->getProducts([], ['page'=>1,'limit'=>1000]);

            foreach ($page->items as $dto) {
                Product::updateOrCreate(
                    ['store_id' => $store->id, 'external_id' => $dto->externalId],
                    [
                      'name'       => $dto->name,
                      'sku'        => $dto->sku,
                      'price'      => $dto->price,
                      'currency'   => $dto->currency,
                      'image_url'  => $dto->imageUrl,
                      'stock'      => 0,
                    ]
                );
            }

            $store->update([
                'last_synced_at'   => now(),
                'last_sync_status' => 'success',
            ]);

            Log::channel('sync')->info('sync.products.success', [
                'store_id'      => $store->id,
                'synced_count'  => count($page->items),
            ]);

        } catch (Throwable $e) {
            $store->update([
                'last_synced_at'   => now(),
                'last_sync_status' => 'failed',
            ]);

            Log::channel('sync')->error('sync.products.failed', [
                'store_id' => $store->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public function syncOrders(Store $store): void
    {
        Log::channel('sync')->info('sync.orders.start', ['store_id' => $store->id]);
        try {
            $client = $this->factory->for($store);
            $page   = $client->getOrders(
                now()->subDays(30), now(), ['page'=>1,'limit'=>1000]
            );

            foreach ($page->items as $dto) {
                Order::updateOrCreate(
                    ['store_id' => $store->id, 'external_id' => $dto->externalId],
                    [
                      'number'             => $dto->number,
                      'status'             => $dto->status,
                      'customer_name'      => $dto->customerName,
                      'customer_email'     => $dto->customerEmail,
                      'total'              => $dto->total,
                      'currency'           => $dto->currency,
                      'external_created_at'=> $dto->createdAt,
                    ]
                );
            }

            $store->update([
                'last_synced_at'   => now(),
                'last_sync_status' => 'success',
            ]);

            Log::channel('sync')->info('sync.orders.success', [
                'store_id'     => $store->id,
                'synced_count' => count($page->items),
            ]);

        } catch (Throwable $e) {
            $store->update([
                'last_synced_at'   => now(),
                'last_sync_status' => 'failed',
            ]);

            Log::channel('sync')->error('sync.orders.failed', [
                'store_id' => $store->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
