<?php

namespace App\Jobs;

use App\Domain\Enums\ExportStatus;
use App\Services\ProductService;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Excel as ExcelService;
use Maatwebsite\Excel\Facades\Excel;  
use Illuminate\Support\Facades\Log;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(private Export $export) {}

    public function handle(ProductService $productService): void
    {
        $this->export->update([
            'status'     => ExportStatus::PROCESSING,
            'started_at' => now(),
        ]);

        Log::channel('exports')->info('export started', [
            'export_id' => $this->export->id,
            'store_id'  => $this->export->store_id,
            'type'      => $this->export->type->value,
            'format'    => $this->export->meta['format'],
        ]);

        $page = $productService->list($this->export->store, [], ['page' => 1, 'limit' => 1000]);

        $format  = $this->export->meta['format'] ?? 'csv';
        $rows    = [];
        $headings = ['Nombre','SKU','Precio','Moneda','Imagen'];

        foreach ($page->items as $p) {
            $rows[] = [$p->name, $p->sku, $p->price, $p->currency, $p->imageUrl];
        }

        $exportDir = storage_path('app/private/exports');
        if (! is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $filename = 'exports/products_'.$this->export->id.'_'.Str::random(6).'.'.$format;
        if ($format === 'xlsx') {
            Excel::store(
            new ArrayExport($headings, $rows, $filename),
            $filename,
            'local',
            ExcelService::XLSX
            );
        } else {
            $handle = fopen(Storage::path($filename), 'w+');
            fputcsv($handle, $headings);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }

        Log::channel('exports')->info('export finished', [
            'export_id'   => $this->export->id,
            'store_id'    => $this->export->store_id,
            'path'        => $filename,
            'duration_ms' => $this->batch()?->metrics()->runtime() ?? null,
        ]);

        $this->export->update([
            'path'        => $filename,
            'status'      => ExportStatus::DONE,
            'finished_at' => now(),
        ]);
    }
}
