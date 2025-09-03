<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Productos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 space-y-4 max-w-4xl mx-auto">
    
    <form
      id="filters-form"
      hx-get="{{ route('products.fragment', $store) }}"
      hx-target="#products-list"
      hx-trigger="submit"
      class="grid grid-cols-4 gap-3"
    >
      <input
        type="text"
        name="q"
        value="{{ $filters['q'] ?? '' }}"
        placeholder="Buscar..."
        class="border p-2 rounded col-span-2"
      />

      <input
        type="number"
        name="min_price"
        value="{{ $filters['min_price'] ?? '' }}"
        placeholder="Precio min."
        class="border p-2 rounded"
        min="0"
      />

      <input
        type="number"
        name="max_price"
        value="{{ $filters['max_price'] ?? '' }}"
        placeholder="Precio max."
        class="border p-2 rounded"
        min="0"
      />

      <select name="currency" class="border p-2 rounded">
        <option value="">Moneda</option>
        <option value="USD" @selected(($filters['currency'] ?? '')==='USD')>USD</option>
        <option value="EUR" @selected(($filters['currency'] ?? '')==='EUR')>EUR</option>
      </select>

      {{-- campos de paginación ocultos --}}
      <input type="hidden" name="page"  value="{{ $filters['page']  ?? 1 }}" />
      <input type="hidden" name="limit" value="{{ $filters['limit'] ?? 10 }}" />

      <button
        type="submit"
        class="col-span-4 bg-indigo-600 text-white py-2 rounded"
      >
        Filtrar
      </button>
    </form>


    <div
      id="products-list"
      hx-get="{{ route('products.fragment', $store) }}"
      hx-trigger="load, keyup from:#filters-form delay:400ms"
      hx-target="#products-list"
      hx-include="#filters-form"
    >
      @include('products.partials.list', compact('store','pageDTO','filters'))
    </div>

    <div class="mt-4 flex items-center gap-4">
      <form method="POST" action="{{ route('exports.store', $store) }}">
        @csrf
        <input type="hidden" name="type" value="products">
        <input type="hidden" name="format" value="csv">
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">
          Exportar CSV
        </button>
      </form>

      <form method="POST" action="{{ route('exports.store', $store) }}">
        @csrf
        <input type="hidden" name="type" value="products">
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
