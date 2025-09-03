<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\SyncService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private SyncService $sync) {}

    public function index(Request $request)
    {
        $from     = $request->input('from') ? now()->parse($request->input('from')) : now()->subDays(30);
        $to       = $request->input('to')   ? now()->parse($request->input('to'))   : now();
        $platform = $request->input('platform');

        $stores = Store::where('status', 'connected')
            ->when($platform, fn($q) => $q->where('platform', $platform))
            ->get();

        $summaries = [];
        foreach ($stores as $store) {

            $this->sync->syncProducts($store);
            $this->sync->syncOrders($store);

            $productCount = $store->products()->count();
            $ordersCount  = $store->orders()
                ->whereBetween('external_created_at', [$from, $to])->count();
            $salesTotal   = $store->orders()
                ->whereBetween('external_created_at', [$from, $to])
                ->sum('total');

            $summaries[$store->id] = compact('productCount','ordersCount','salesTotal');
        }

        return view('dashboard', compact('stores','summaries','from','to','platform'));
    }
}
