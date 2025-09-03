<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Pedidos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 space-y-4 max-w-4xl mx-auto">

    <form
      id="orders-filters-form"
      hx-get="{{ route('orders.fragment', $store) }}"
      hx-target="#orders-list"
      hx-swap="innerHTML"
      class="grid grid-cols-3 gap-4 mb-6"
    >
      <input
        type="text"
        name="q"
        value="{{ $filters['q'] ?? '' }}"
        placeholder="Buscar..."
        class="border p-2 rounded col-span-2"
      />
      <input
        type="date"
        name="from"
        value="{{ request('from') }}"
        class="border p-2 rounded"
        placeholder="Desde"
      >
      <input
        type="date"
        name="to"
        value="{{ request('to') }}"
        class="border p-2 rounded"
        placeholder="Hasta"
      >
      <input
        type="text"
        name="status"
        value="{{ request('status') }}"
        class="border p-2 rounded"
        placeholder="Estado"
      >
      <input
        type="number"
        step="0.01"
        name="min_total"
        value="{{ request('min_total') }}"
        class="border p-2 rounded"
        placeholder="Min total"
      >
      <input
        type="number"
        step="0.01"
        name="max_total"
        value="{{ request('max_total') }}"
        class="border p-2 rounded"
        placeholder="Max total"
      >
      <button
        type="submit"
        class="col-span-3 bg-blue-600 text-white px-4 py-2 rounded"
      >
        Filtrar
      </button>
    </form>


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
          Exportar CSV
        </button>
      </form>

      <form method="POST" action="{{ route('exports.store', $store) }}">
        @csrf
        <input type="hidden" name="type" value="orders">
        <input type="hidden" name="format" value="xlsx">
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">
          Exportar xlsx
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
