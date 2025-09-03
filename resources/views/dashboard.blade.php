
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

    <div class="mt-6">
      <canvas id="metricsBarChart" height="120"></canvas>
    </div>

    <div class="mt-6">
      <canvas id="salesDoughnutChart" height="120"></canvas>
    </div>

    @forelse($stores as $store)
      <div class="border rounded-lg p-4 shadow-sm">
        <h3 class="text-lg font-bold">{{ $store->name }} ({{ ucfirst($store->platform->value) }})</h3>
        <div class="grid grid-cols-3 gap-4 mt-4">

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

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const stores = @json($stores->pluck('name'));
      const productCounts = @json($stores->map(fn($s) => $summaries[$s->id]['productCount']));
      const orderCounts = @json($stores->map(fn($s) => $summaries[$s->id]['orderCount']));
      const salesTotals = @json($stores->map(fn($s) => $summaries[$s->id]['salesTotal']));

      new Chart(document.getElementById('metricsBarChart'), {
        type: 'bar',
        data: {
          labels: stores,
          datasets: [
            {
              label: 'Productos',
              data: productCounts,
              backgroundColor: 'rgba(54, 162, 235, 0.6)',
            },
            {
              label: 'Pedidos',
              data: orderCounts,
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
      });

      new Chart(document.getElementById('salesDoughnutChart'), {
        type: 'doughnut',
        data: {
          labels: stores,
          datasets: [{
            label: 'Ventas Totales',
            data: salesTotals,
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
      });
    });
  </script>
  @endpush

</x-app-layout>
