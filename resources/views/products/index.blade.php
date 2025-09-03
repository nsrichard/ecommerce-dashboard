<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Productos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 space-y-4 max-w-4xl mx-auto">
    
    <div class="flex items-center gap-3">
      <input
        type="text" name="q" placeholder="Buscar..."
        class="border p-2 rounded w-full"
        hx-get="{{ route('products.fragment', $store) }}"
        hx-trigger="keyup changed delay:400ms"
        hx-target="#products-list"
      />
    </div>

    <div
      id="products-list"
      hx-get="{{ route('products.fragment', $store) }}"
      hx-trigger="load"
      hx-target="#products-list"
    >
      @include('products.partials.list', ['pageDTO' => $pageDTO, 'store' => $store])
    </div>

    <div class="mt-4 flex items-center gap-4">
      <form method="POST" action="{{ route('exports.store', $store) }}">
        @csrf
        <input type="hidden" name="type" value="products">
        <input type="hidden" name="format" value="csv">
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">
          Exportar Productos CSV
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
