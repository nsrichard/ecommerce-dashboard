<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(private readonly OrderService $service) {}

    public function index(Store $store, Request $request)
    {
        $pageDTO = $this->service->listRecent($store, null, null, [
            'page' => (int)$request->integer('page', 1),
            'limit' => (int)$request->integer('limit', 25),
        ]);

        return view('orders.index', compact('store', 'pageDTO'));
    }

    public function fragment(Store $store, Request $request)
    {
        $pageDTO = $this->service->listRecent($store, null, null, [
            'page' => (int)$request->integer('page', 1),
            'limit' => (int)$request->integer('limit', 25),
        ]);

        return view('orders.partials.list', compact('pageDTO', 'store'));
    }
}
