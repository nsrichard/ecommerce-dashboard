@if(empty($pageDTO->items))
  <p class="text-gray-600">No hay pedidos</p>
@else
  <table class="min-w-full border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border text-left">Número</th>
        <th class="p-2 border text-left">Cliente</th>
        <th class="p-2 border text-left">Estado</th>
        <th class="p-2 border text-left">Fecha</th>
        <th class="p-2 border text-right">Total</th>
      </tr>
    </thead>
    <tbody>
    @foreach($pageDTO->items as $o)
      <tr class="border-b">
        <td class="p-2 border">{{ $o->number }}</td>
        <td class="p-2 border">{{ $o->customerName }}</td>
        <td class="p-2 border">{{ $o->status }}</td>
        <td class="p-2 border">{{ $o->createdAt->format('Y-m-d H:i') }}</td>
        <td class="p-2 border text-right">{{ number_format($o->total, 2) }} {{ $o->currency }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endif
