<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Order;
use App\Http\Requests\OrdersFilterRequest;
use App\Services\SyncService;
use App\Support\DTO\PageDTO;

class OrdersController extends Controller
{
    public function __construct(
        private readonly SyncService $sync
    ) {}

    public function index(Store $store, OrdersFilterRequest $request)
    {
        $this->sync->syncOrders($store);

        $pageDTO = $this->buildPageDTO($store->id, $request->filters(), $request->get('limit', 10));

        return view('orders.index',[
            'store'   => $store,
            'pageDTO' => $pageDTO,
            'filters' => $request->filters(),
        ]);
    }

    public function fragment(Store $store, OrdersFilterRequest $request)
    {
        $pageDTO = $this->buildPageDTO($store->id, $request->filters(), $request->get('limit', 10));

        return view('orders.partials.list', [
            'store'   => $store,
            'pageDTO' => $pageDTO,
            'filters' => $request->filters(),
        ]);
    }

    private function buildPageDTO(int $storeId, array $filters, int $limit): PageDTO
    {
        $paginator = Order::where('store_id', $storeId)
            ->filter($filters)
            ->paginate($limit)
            ->appends($filters);

        return PageDTO::fromPaginator($paginator);
    }
}
