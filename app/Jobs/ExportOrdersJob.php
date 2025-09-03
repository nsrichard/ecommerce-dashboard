<?php

namespace App\Jobs;

use App\Domain\Enums\ExportStatus;
use App\Services\OrderService;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ExportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Export $export) {}

    public function handle(OrderService $orderService): void
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

        $page = $orderService->listRecent($this->export->store, null, null, ['page' => 1, 'limit' => 1000]);

        $format  = $this->export->meta['format'] ?? 'csv';
        $rows    = [];
        $headings = ['Número','Cliente','Estado','Fecha','Total','Moneda'];

        foreach ($page->items as $o) {
            $rows[] = [
                $o->number,
                $o->customerName,
                $o->status,
                $o->createdAt->format('Y-m-d H:i'),
                number_format($o->total, 2),
                $o->currency,
            ];
        }

        $filename = 'exports/orders_'.$this->export->id.'_'.Str::random(6).'.'.$format;
        if ($format === 'xlsx') {
            Excel::store(
            new ArrayExport($headings, $rows, $filename),
            $filename,
            'local',
            Excel::XLSX
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
