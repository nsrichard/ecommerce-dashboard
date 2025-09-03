<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Dashboard</h2>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto space-y-6">

    <form method="GET" action="{{ route('dashboard') }}"
          class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6 px-4">

      <!-- Desde -->
      <div class="flex flex-col">
        <label for="from" class="text-sm font-medium text-gray-700 mb-1">Desde</label>
        <input
          id="from"
          type="date"
          name="from"
          value="{{ $from->format('Y-m-d') }}"
          class="block w-full rounded border-gray-300 shadow-sm 
                focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
        >
      </div>

      <!-- Hasta -->
      <div class="flex flex-col">
        <label for="to" class="text-sm font-medium text-gray-700 mb-1">Hasta</label>
        <input
          id="to"
          type="date"
          name="to"
          value="{{ $to->format('Y-m-d') }}"
          class="block w-full rounded border-gray-300 shadow-sm 
                focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
        >
      </div>

      <!-- Plataforma -->
      <div class="flex flex-col">
        <label for="platform" class="text-sm font-medium text-gray-700 mb-1">Plataforma</label>
        <select
          id="platform"
          name="platform"
          class="block w-full rounded border-gray-300 shadow-sm 
                focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Todas las plataformas</option>
          <option value="shopify"    @selected($platform === 'shopify')>Shopify</option>
          <option value="woocommerce" @selected($platform === 'woocommerce')>WooCommerce</option>
        </select>
      </div>

      <!-- Botón Filtrar -->
      <div class="flex items-end">
        <button
          type="submit"
          class="w-full inline-flex justify-center items-center
                bg-blue-600 hover:bg-blue-700 text-white font-semibold
                py-2 px-4 rounded shadow-lg focus:outline-none 
                focus:ring-2 focus:ring-blue-500 transition"
        >
          Filtrar
        </button>
      </div>

    </form>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <div class="bg-white border rounded-lg p-4 shadow-sm">
        <canvas id="metricsBarChart" height="120"></canvas>
      </div>

      <div class="bg-white border rounded-lg p-4 shadow-sm">
        <canvas id="salesDoughnutChart" height="120"></canvas>
      </div>

      <div class="space-y-4">
        @forelse($stores as $store)
          <div class="bg-white border rounded-lg p-4 shadow-sm">
            <h3 class="text-lg font-bold">
              {{ $store->name }} ({{ ucfirst($store->platform->value) }})
            </h3>
            <div class="grid grid-cols-3 gap-2 mt-3 text-center">
              <div>
                <p class="text-sm text-gray-600">Productos</p>
                <p class="text-2xl">{{ $summaries[$store->id]['productCount'] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Pedidos</p>
                <p class="text-2xl">{{ $summaries[$store->id]['ordersCount'] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Ventas</p>
                <p class="text-2xl">
                  ${{ number_format($summaries[$store->id]['salesTotal'], 2) }}
                </p>
              </div>
            </div>
          </div>
        @empty
          <p class="text-gray-600">Sin datos de tiendas para mostrar.</p>
        @endforelse
      </div>

    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const names   = @json($stores->pluck('name'));
        const prod    = @json($stores->map(fn($s) => $summaries[$s->id]['productCount']));
        const orders  = @json($stores->map(fn($s) => $summaries[$s->id]['ordersCount']));
        const sales   = @json($stores->map(fn($s) => $summaries[$s->id]['salesTotal']));

        new Chart(
          document.getElementById('metricsBarChart'),
          {
            type: 'bar',
            data: {
              labels: names,
              datasets: [
                {
                  label: 'Productos',
                  data: prod,
                  backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                  label: 'Pedidos',
                  data: orders,
                  backgroundColor: 'rgba(255, 99, 132, 0.6)',
                }
              ]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Productos y Pedidos por Tienda' }
              }
            }
          }
        );

        new Chart(
          document.getElementById('salesDoughnutChart'),
          {
            type: 'doughnut',
            data: {
              labels: names,
              datasets: [{
                label: 'Ventas Totales',
                data: sales,
                backgroundColor: [
                  'rgba(255, 99, 132, 0.6)',
                  'rgba(54, 162, 235, 0.6)',
                  'rgba(255, 206, 86, 0.6)',
                  'rgba(75, 192, 192, 0.6)',
                  'rgba(153, 102, 255, 0.6)',
                  'rgba(255, 159, 64, 0.6)'
                ],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Distribución de Ventas Totales' }
              }
            }
          }
        );
      });
    </script>
  @endpush

</x-app-layout>
