<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Pedidos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6">
    <div id="orders-list" hx-get="{{ route('orders.fragment', $store) }}" hx-trigger="load" hx-target="#orders-list">
      @include('orders.partials.list', ['pageDTO' => $pageDTO, 'store' => $store])
    </div>
  </div>
</x-app-layout>
