<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use App\Services\ExportService;
use App\Models\Store;
use App\Models\Export;
use App\Domain\Enums\ExportStatus;
use App\Domain\Enums\ExportType;
use App\Jobs\ExportProductsJob;
use App\Jobs\ExportOrdersJob;
use PHPUnit\Framework\Attributes\Test;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_products_export_and_dispatches_job(): void
    {
        Queue::fake();

        $storeId = DB::table('stores')->insertGetId([
            'name'       => 'Tienda X',
            'platform'   => 'web',
            'domain'     => 'x.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $store = Store::findOrFail($storeId);

        $service = new ExportService();
        $export  = $service->createExport(
            $store,
            ExportType::PRODUCTS->value,
            'csv',
            ['category' => 'A']
        );

        $this->assertInstanceOf(Export::class, $export);
        $this->assertSame($store->id, $export->store_id);
        $this->assertSame(ExportType::PRODUCTS, $export->type);
        $this->assertSame(ExportStatus::QUEUED,   $export->status);
        $this->assertSame('csv',                  $export->meta['format']);
        $this->assertSame(['category' => 'A'],    $export->meta['filters']);
        $this->assertNull($export->path);
        $this->assertNull($export->started_at);
        $this->assertNull($export->finished_at);

        // 4. Ensure a record exists in the database
        Queue::assertPushed(ExportProductsJob::class, function ($job) use ($export) {
            $rp = new \ReflectionProperty($job, 'export');
            $rp->setAccessible(true);
            return $rp->getValue($job)->is($export);
        });
    }

    #[Test]
    public function it_creates_an_orders_export_and_dispatches_job(): void
    {
        Queue::fake();

        $storeId = DB::table('stores')->insertGetId([
            'name'       => 'Tienda Y',
            'platform'   => 'web',
            'domain'     => 'y.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $store = Store::findOrFail($storeId);

        $service = new ExportService();
        $export  = $service->createExport(
            $store,
            ExportType::ORDERS->value,
            'xlsx',
            ['date_from' => '2025-01-01']
        );

        $this->assertInstanceOf(Export::class, $export);
        $this->assertSame($store->id,               $export->store_id);
        $this->assertSame(ExportType::ORDERS,       $export->type);
        $this->assertSame(ExportStatus::QUEUED,     $export->status);
        $this->assertSame('xlsx',                   $export->meta['format']);
        $this->assertSame(['date_from' => '2025-01-01'], $export->meta['filters']);

        Queue::assertPushed(ExportOrdersJob::class, function ($job) use ($export) {
            $rp = new \ReflectionProperty($job, 'export');
            $rp->setAccessible(true);
            return $rp->getValue($job)->is($export);
        });
    }
}
