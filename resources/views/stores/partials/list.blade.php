<table class="min-w-full border mt-4">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2 border">Nombre</th>
      <th class="p-2 border">Plataforma</th>
      <th class="p-2 border">Dominio</th>
      <th class="p-2 border">Estado</th>
      <th class="p-2 border">Acciones</th>
      <th class="p-2 border">Ver</th>
    </tr>
  </thead>
  <tbody>
    @forelse($stores as $store)
      <tr class="border-b">
        <td class="p-2 border">{{ $store->name }}</td>
        <td class="p-2 border capitalize">{{ $store->platform->value }}</td>
        <td class="p-2 border">{{ $store->domain }}</td>
        <td class="p-2 border">{{ $store->status }}</td>

        {{-- Acciones de Conexión --}}
        <td class="p-2 border">
          @if($store->status === 'disconnected')
            @if($store->platform->value === 'shopify')
              <a href="{{ route('oauth.shopify.redirect', $store) }}"
                 class="text-blue-600 underline">Conectar Shopify</a>
            @else
              <a href="{{ route('stores.connect.woocommerce', $store) }}"
                 class="text-blue-600 underline">Conectar WooCommerce</a>
            @endif
          @else
            <span class="text-green-600">Conectada</span>
          @endif
        </td>

        <td class="p-2 border space-x-2">
          <a href="{{ route('products.index', $store) }}"
             class="text-indigo-600">Productos</a>
          <a href="{{ route('orders.index', $store) }}"
             class="text-indigo-600">Pedidos</a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="p-4 text-center text-gray-600">
          Sin registros
        </td>
      </tr>
    @endforelse
  </tbody>
</table>
