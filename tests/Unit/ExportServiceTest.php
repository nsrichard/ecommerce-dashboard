<?php

namespace Tests\Unit;

use App\Domain\Enums\ExportType;
use App\Jobs\ExportOrdersJob;
use App\Jobs\ExportProductsJob;
use App\Models\Export;
use App\Models\Store;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_products_export_dispatches_job_and_records(): void
    {
        Bus::fake();

        $store = Store::factory()->create();
        $service = $this->app->make(ExportService::class);

        $export = $service->createExport($store, ExportType::PRODUCTS->value, 'csv');

        $this->assertDatabaseHas('exports', [
            'id'     => $export->id,
            'type'   => ExportType::PRODUCTS->value,
            'status' => 'queued',
        ]);

        Bus::assertDispatched(ExportProductsJob::class, fn($job) => $job->export->id === $export->id);
    }

    public function test_create_orders_export_dispatches_job(): void
    {
        Bus::fake();

        $store = Store::factory()->create();
        $service = $this->app->make(ExportService::class);

        $export = $service->createExport($store, ExportType::ORDERS->value, 'xlsx');

        $this->assertEquals('xlsx', $export->meta['format']);
        Bus::assertDispatched(ExportOrdersJob::class, fn($job) => $job->export->id === $export->id);
    }
}
