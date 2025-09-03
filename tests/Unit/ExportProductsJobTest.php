<?php

namespace Tests\Unit;

use App\Domain\Enums\ExportStatus;
use App\Jobs\ExportProductsJob;
use App\Models\Export;
use App\Models\Store;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportProductsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_generates_csv_and_updates_export(): void
    {
        Storage::fake('local');
        $store  = Store::factory()->create();
        $export = Export::factory()->create([
            'store_id' => $store->id,
            'type'     => 'products',
            'status'   => ExportStatus::QUEUED,
            'meta'     => ['format' => 'csv', 'filters' => []],
        ]);

        $fakePage = new \App\Support\DTO\PageDTO([],1,1);
        $mockSvc = $this->createMock(ProductService::class);
        $mockSvc->method('list')->willReturn($fakePage);

        (new ExportProductsJob($export))->handle($mockSvc);

        $export->refresh();
        $this->assertEquals(ExportStatus::DONE, $export->status->value);
        Storage::disk('local')->assertExists($export->path);
    }
}
