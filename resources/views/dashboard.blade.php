
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Dashboard</h2>
  </x-slot>

  <div class="py-6 max-w-5xl mx-auto space-y-6">

    <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-2">
      <div>
        <label class="block text-sm">Desde</label>
        <input type="date" name="from"
               value="{{ $from->format('Y-m-d') }}"
               class="border p-1 rounded">
      </div>
      <div>
        <label class="block text-sm">Hasta</label>
        <input type="date" name="to"
               value="{{ $to->format('Y-m-d') }}"
               class="border p-1 rounded">
      </div>
      <button type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded">
        Filtrar
      </button>
    </form>

    @forelse($stores as $store)
      <div class="border rounded-lg p-4 shadow-sm">
        <h3 class="text-lg font-bold">{{ $store->name }} ({{ ucfirst($store->platform->value) }})</h3>
        <div class="grid grid-cols-3 gap-4 mt-4">
          {{-- Productos Totales --}}
          <div class="bg-gray-100 p-4 rounded text-center">
            <p class="text-sm text-gray-600">Productos</p>
            <p class="text-2xl">{{ $summaries[$store->id]['productCount'] }}</p>
          </div>

          <div class="bg-gray-100 p-4 rounded text-center">
            <p class="text-sm text-gray-600">
              Pedidos ({{ $from->format('Y-m-d') }} – {{ $to->format('Y-m-d') }})
            </p>
            <p class="text-2xl">{{ $summaries[$store->id]['orderCount'] }}</p>
          </div>

          <div class="bg-gray-100 p-4 rounded text-center">
            <p class="text-sm text-gray-600">Ventas Totales</p>
            <p class="text-2xl">
              ${{ number_format($summaries[$store->id]['salesTotal'], 2) }}
            </p>
          </div>
        </div>
      </div>
    @empty
      <p class="text-gray-600">Sin datos</p>
    @endforelse
  </div>
</x-app-layout>
