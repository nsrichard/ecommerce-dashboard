<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Pedidos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 space-y-4 max-w-4xl mx-auto">

    <div
      id="orders-list"
      hx-get="{{ route('orders.fragment', $store) }}"
      hx-trigger="load"
      hx-target="#orders-list"
    >
      @include('orders.partials.list', ['pageDTO' => $pageDTO, 'store' => $store])
    </div>

    <div class="mt-4 flex items-center gap-4">
      <form method="POST" action="{{ route('exports.store', $store) }}">
        @csrf
        <input type="hidden" name="type" value="orders">
        <input type="hidden" name="format" value="csv">
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">
          Exportar Pedidos CSV
        </button>
      </form>

      <button
        class="px-3 py-1 border rounded"
        hx-get="{{ route('exports.fragment', $store) }}"
        hx-target="#exports-list"
        hx-swap="innerHTML"
      >
        Ver exports
      </button>
    </div>

    <div id="exports-list" class="mt-4">
      @include('exports.partials.list', ['exports' => $store->exports()->latest()->get()])
    </div>
  </div>
</x-app-layout>
