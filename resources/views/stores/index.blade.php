<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Tiendas</h2>
  </x-slot>

  <div class="py-6 space-y-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('stores.create') }}" class="underline text-blue-600">+ tienda</a>

      {{-- Botón para refrescar con HTMX (opcional) --}}
      <button
        class="px-3 py-1 border rounded"
        hx-get="{{ route('stores.fragment') }}"
        hx-target="#stores-list"
        hx-swap="innerHTML"
      >
        Actualizar
      </button>
    </div>

    <div id="stores-list">
      @include('stores.partials.list', ['stores' => $stores])
    </div>
  </div>
</x-app-layout>
