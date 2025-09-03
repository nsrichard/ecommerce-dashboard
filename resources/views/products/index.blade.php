<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Productos — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 space-y-4">
    <div class="flex items-center gap-3">
      <input
        type="text" name="q" placeholder="Buscar..."
        class="border p-2"
        hx-get="{{ route('products.fragment', $store) }}"
        hx-trigger="keyup changed delay:400ms"
        hx-target="#products-list"
      />
    </div>

    <div id="products-list" hx-get="{{ route('products.fragment', $store) }}" hx-trigger="load" hx-target="#products-list">
      @include('products.partials.list', ['pageDTO' => $pageDTO, 'store' => $store])
    </div>
  </div>
</x-app-layout>
