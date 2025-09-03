<?php

namespace App\Jobs;

use App\Domain\Enums\ExportStatus;
use App\Services\ProductService;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Export $export) {}

    public function handle(ProductService $productService): void
    {
        $this->export->update([
            'status'     => ExportStatus::PROCESSING,
            'started_at' => now(),
        ]);

        $page = $productService->list($this->export->store, [], ['page' => 1, 'limit' => 1000]);

        $filename = 'exports/products_'.$this->export->id.'_'.Str::random(6).'.csv';
        $handle   = fopen(Storage::path($filename), 'w+');

        fputcsv($handle, ['Nombre','SKU','Precio','Moneda','Imagen']);
        foreach ($page->items as $p) {
            fputcsv($handle, [
                $p->name,
                $p->sku,
                number_format($p->price, 2),
                $p->currency,
                $p->imageUrl,
            ]);
        }
        fclose($handle);

        $this->export->update([
            'path'        => $filename,
            'status'      => ExportStatus::DONE,
            'finished_at' => now(),
        ]);
    }
}
