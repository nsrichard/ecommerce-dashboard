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
          <td class="p-2 border">{{ $store->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
