<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Store;
use App\Models\Export;
use App\Enums\ExportStatus;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelService;
use App\Exports\ArrayExport;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(private readonly Export $export) {}

    public function handle(): void
    {
        $this->export->update([
            'status'     => ExportStatus::PROCESSING,
            'started_at' => now(),
        ]);

        Log::channel('exports')->info('export started', [
            'export_id' => $this->export->id,
            'store_id'  => $this->export->store_id,
            'format'    => $this->export->meta['format'] ?? 'csv',
        ]);

        $disk     = Storage::disk('local');
        $folder   = 'private/exports';
        $disk->exists($folder) || $disk->makeDirectory($folder, 0755, true);

        $format   = $this->export->meta['format'] ?? 'csv';
        $random   = Str::random(6);
        $filename = "{$folder}/products_{$this->export->id}_{$random}.{$format}";

        $headings = ['Nombre', 'SKU', 'Precio', 'Moneda', 'Imagen'];
        $rows     = [];

        Product::where('store_id', $this->export->store_id)
            ->orderBy('id')
            ->chunk(500, function ($products) use (&$rows) {
                foreach ($products as $p) {
                    $rows[] = [
                        $p->name,
                        $p->sku,
                        $p->price,
                        $p->currency,
                        $p->image_url,
                    ];
                }
            });

        if ($format === 'xlsx') {
            Excel::store(
                new ArrayExport($headings, $rows),
                $filename,
                'local',
                ExcelService::XLSX
            );
        } else {
            $path   = $disk->path($filename);
            $handle = fopen($path, 'w+');
            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }

        Log::channel('exports')->info('export finished', [
            'export_id'   => $this->export->id,
            'store_id'    => $this->export->store_id,
            'path'         => $filename,
            'duration_ms' => $this->batch()?->metrics()->runtime() ?? null,
        ]);

        $this->export->update([
            'path'         => $filename,
            'status'       => ExportStatus::DONE,
            'finished_at'  => now(),
        ]);
    }
}
