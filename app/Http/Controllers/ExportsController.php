<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Export;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExportsController extends Controller
{
    public function store(Request $request, Store $store, ExportService $exportService)
    {
        $data = $request->validate([
            'type'   => ['required', Rule::in(['products','orders'])],
            'format' => ['required', Rule::in(['csv', 'xlsx'])],
        ]);

        $exportService->createExport($store, $data['type'], $data['format']);

        return back()->with('status', 'Export encolado. Actualiza para ver progreso.');
    }

    public function download(Export $export)
    {
        abort_if($export->status->value !== 'done', 404);
        return Storage::download($export->path);
    }

    public function fragment(Store $store)
    {
        $exports = $store->exports()->latest()->get();
        return view('exports.partials.list', compact('exports'));
    }
}
