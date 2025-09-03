<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Tiendas</h2>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto space-y-6">

    <div class="flex justify-between items-center">

      <a
        href="{{ route('stores.create') }}"
        class="inline-flex items-center gap-1
               bg-blue-600 hover:bg-blue-700 text-white
               font-medium px-4 py-2 rounded-lg shadow-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
      >
        + Tienda
      </a>

      <button
        hx-get="{{ route('stores.fragment') }}"
        hx-target="#stores-list"
        hx-swap="innerHTML"
        class="inline-flex items-center gap-1
               bg-green-500 hover:bg-green-600 text-white
               font-medium px-4 py-2 rounded-lg shadow
               focus:outline-none focus:ring-2 focus:ring-green-400 transition
               animate-pulse"
      >
        Actualizar
      </button>
    </div>

    <div id="stores-list">
      @include('stores.partials.list', ['stores' => $stores])
    </div>

  </div>
</x-app-layout>
