<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Http\Requests\ProductsFilterRequest;
use App\Services\SyncService;
use App\Support\DTO\PageDTO;

class ProductsController extends Controller
{
    public function __construct(
        private readonly SyncService $sync
    ) {}

    public function index(Store $store, ProductsFilterRequest $request)
    {
        $this->sync->syncProducts($store);

        $pageDTO = $this->buildPageDTO($store->id, $request->filters(), $request->get('limit', 10));

        return view('products.index', [
            'store'   => $store,
            'pageDTO' => $pageDTO,
            'filters' => $request->filters(),
        ]);
    }

    public function fragment(Store $store, ProductsFilterRequest $request)
    {
        $pageDTO = $this->buildPageDTO($store->id, $request->filters(), $request->get('limit', 10));

        return view('products.partials.list', [
            'store'   => $store,
            'pageDTO' => $pageDTO,
            'filters' => $request->filters(),
        ]);
    }

    private function buildPageDTO(int $storeId, array $filters, int $limit): PageDTO
    {
        $paginator = Product::where('store_id', $storeId)
            ->filter($filters)
            ->orderBy('name')
            ->paginate($limit)
            ->appends($filters);

        return PageDTO::fromPaginator($paginator);
    }
}
