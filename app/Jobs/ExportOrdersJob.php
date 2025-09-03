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

        $page = $orderService->listRecent($this->export->store, null, null, ['page' => 1, 'limit' => 1000]);

        $filename = 'exports/orders_'.$this->export->id.'_'.Str::random(6).'.csv';
        $handle   = fopen(Storage::path($filename), 'w+');
        fputcsv($handle, ['Número','Cliente','Estado','Fecha','Total','Moneda']);
        foreach ($page->items as $o) {
            fputcsv($handle, [
                $o->number,
                $o->customerName,
                $o->status,
                $o->createdAt->format('Y-m-d H:i'),
                number_format($o->total, 2),
                $o->currency,
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
