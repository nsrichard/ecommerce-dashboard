<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\MetricService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, MetricService $metrics)
    {
        $from = $request->input('from')
            ? new \DateTimeImmutable($request->input('from'))
            : now()->subDays(30);
        $to = $request->input('to')
            ? new \DateTimeImmutable($request->input('to'))
            : now();

        $stores    = Store::where('status', 'connected')->get();
        $summaries = [];
        foreach ($stores as $store) {
            $summaries[$store->id] = $metrics->summary($store, $from, $to);
        }

        return view('dashboard', compact('stores', 'summaries', 'from', 'to'));
    }
}
