<?php

namespace App\Services;

use App\Domain\Enums\ExportStatus;
use App\Domain\Enums\ExportType;
use App\Jobs\ExportOrdersJob;
use App\Jobs\ExportProductsJob;
use App\Models\Store;
use App\Models\Export;
use Illuminate\Support\Str;

class ExportService
{
    public function createExport(Store $store, string $type, string $format = 'csv', array $filters = []): Export
    {
        // Crear registro en tabla exports
        $export = $store->exports()->create([
            'type'       => ExportType::from($type),
            'status'     => ExportStatus::QUEUED,
            'meta'       => ['format' => $format, 'filters' => $filters],
            'path'       => null,
            'started_at' => null,
            'finished_at'=> null,
        ]);

        // Disparar job correspondiente
        if ($type === ExportType::PRODUCTS->value) {
            ExportProductsJob::dispatch($export);
        } else {
            ExportOrdersJob::dispatch($export);
        }

        return $export;
    }
}
