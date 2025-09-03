<?php

namespace App\Jobs;

use App\Domain\Enums\ExportStatus;
use App\Models\Export;
use App\Models\Order;
use App\Models\Store;
use App\Services\SyncService;
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

class ExportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(private readonly Export $export) {}

    public function handle(OrderService $orderService): void
    {
        $this->export->update([
            'status'     => SyncService::PROCESSING,
            'started_at' => now(),
        ]);

        Log::channel('exports')->info('export started', [
            'export_id' => $this->export->id,
            'store_id'  => $this->export->store_id,
            'format'    => $this->export->meta['format'] ?? 'csv',
        ]);

        $store = Store::findOrFail($this->export->store_id);
        $orderService->syncOrders($store);

        $headings = ['Número','Cliente','Estado','Fecha','Total','Moneda'];
        $rows      = [];

        Order::where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->chunk(500, function ($orders) use (&$rows) {
                foreach ($orders as $o) {
                    $rows[] = [
                        $o->number,
                        $o->customer_name,
                        $o->status,
                        $o->created_at->format('Y-m-d H:i'),
                        number_format($o->total, 2),
                        $o->currency,
                    ];
                }
            });

        $disk   = Storage::disk('local');
        $folder = 'private/exports';
        $disk->exists($folder) || $disk->makeDirectory($folder, 0755, true);

        $format   = $this->export->meta['format'] ?? 'csv';
        $random   = Str::random(6);
        $filename = "{$folder}/orders_{$this->export->id}_{$random}.{$format}";

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
