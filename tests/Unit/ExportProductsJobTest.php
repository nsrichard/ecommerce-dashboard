<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Jobs\ExportProductsJob;
use App\Models\Export;
use App\Domain\Enums\ExportStatus;
use App\Services\SyncService;
use PHPUnit\Framework\Attributes\Test;

class ExportProductsJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_a_csv_file_and_marks_export_done(): void
    {
        Storage::fake('local');

        $storeId = DB::table('stores')->insertGetId([
            'name'       => 'Tienda de Prueba',
            'platform'   => 'web',
            'domain'     => 'prueba.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            [
                'store_id'   => $storeId,
                'external_id'=> 0,
                'sku'        => 'sku-1',
                'name'      => 'Producto Uno',
                'price'      => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'store_id'   => $storeId,
                'external_id'=> 1,
                'sku'        => 'sku-2',
                'name'      => 'Producto Dos',
                'price'      => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $exportId = DB::table('exports')->insertGetId([
            'store_id'   => $storeId,
            'type'       => 'products',
            'status'     => ExportStatus::QUEUED,
            'meta'       => json_encode(['format' => 'csv']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $export = Export::findOrFail($exportId);

        $serviceMock = $this->createMock(SyncService::class);
        $serviceMock
            ->expects($this->once())
            ->method('syncProducts')
            ->with($export->store)
            ->willReturnCallback(function () {
            });

        $this->app->instance(SyncService::class, $serviceMock);

        App::call([new ExportProductsJob($export), 'handle']);

        // Aserciones
        $export->refresh();
        Storage::disk('local')->assertExists($export->path);
        $this->assertEquals(ExportStatus::DONE, $export->status );
        $this->assertStringEndsWith('.csv', $export->path);
    }

    #[Test]
    public function it_generates_an_xlsx_file_when_requested(): void
    {
        Storage::fake('local');

        $storeId = DB::table('stores')->insertGetId([
            'name'       => 'Otra Tienda',
            'platform'   => 'web',
            'domain'     => 'otra.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            [
                'store_id'   => $storeId,
                'external_id'=> 0,
                'sku'        => 'sku-A',
                'name'       => 'Prod A',
                'price'      => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $exportId = DB::table('exports')->insertGetId([
            'store_id'   => $storeId,
            'type'       => 'products',
            'status'     => ExportStatus::QUEUED,
            'meta'       => json_encode(['format' => 'xlsx']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $export = Export::findOrFail($exportId);

        $serviceMock = $this->createMock(SyncService::class);
        $serviceMock
            ->method('syncProducts')
            ->with($export->store)
            ->willReturnCallback(function () {
            });

        $this->app->instance(SyncService::class, $serviceMock);

        App::call([new ExportProductsJob($export), 'handle']);

        $export->refresh();
        Storage::disk('local')->assertExists($export->path);
        $this->assertStringEndsWith('.xlsx', $export->path);
        $this->assertEquals(ExportStatus::DONE, $export->status);
    }
}
