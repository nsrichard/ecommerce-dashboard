<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct(private readonly ProductService $service) {}

    public function index(Store $store, Request $request)
    {
        $filters = $request->only(['q']);
        $pageDTO = $this->service->list($store, $filters, [
            'page' => (int)$request->integer('page', 1),
            'limit' => (int)$request->integer('limit', 25),
        ]);

        return view('products.index', compact('store', 'pageDTO'));
    }

    public function fragment(Store $store, Request $request)
    {
        $filters = $request->only(['q']);
        $pageDTO = $this->service->list($store, $filters, [
            'page' => (int)$request->integer('page', 1),
            'limit' => (int)$request->integer('limit', 25),
        ]);

        return view('products.partials.list', compact('pageDTO', 'store'));
    }
}
