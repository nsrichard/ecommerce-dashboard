@if($stores->isEmpty())
  <p class="text-gray-600">No hay tiendas</p>
@else
  <table class="min-w-full border mt-4">
    <thead class="bg-gray-100">
      <tr>
        <th class="text-left p-2 border">Nombre</th>
        <th class="text-left p-2 border">Plataforma</th>
        <th class="text-left p-2 border">Dominio</th>
        <th class="text-left p-2 border">Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($stores as $store)
        <tr class="border-b">
          <td class="p-2 border">{{ $store->name }}</td>
          <td class="p-2 border capitalize">{{ $store->platform->value ?? $store->platform }}</td>
          <td class="p-2 border">{{ $store->domain }}</td>
          <td class="p-2 border">
            @if($store->status === 'disconnected')
              @if($store->platform->value === 'shopify')
                <a href="{{ route('oauth.shopify.redirect', $store) }}"
                  class="text-blue-600 underline">Conectar Shopify</a>
              @else
                <a href="{{ route('stores.connect.woocommerce', $store) }}"
                  class="text-blue-600 underline">Conectar Woo</a>
              @endif
            @else
              <span class="text-green-600">Conectada</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
