@if(empty($pageDTO->items))
  <p class="text-gray-600">No hay productos.</p>
@else
  <table class="min-w-full border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border text-left">Nombre</th>
        <th class="p-2 border text-left">SKU</th>
        <th class="p-2 border text-right">Precio</th>
        <th class="p-2 border text-left">Moneda</th>
        <th class="p-2 border text-left">Imagen</th>
      </tr>
    </thead>
    <tbody>
    @foreach($pageDTO->items as $p)
      <tr class="border-b">
        <td class="p-2 border">{{ $p->name }}</td>
        <td class="p-2 border">{{ $p->sku }}</td>
        <td class="p-2 border text-right">{{ number_format($p->price, 2) }}</td>
        <td class="p-2 border">{{ $p->currency }}</td>
        <td class="p-2 border">
          @if($p->imageUrl)
            <img src="{{ $p->imageUrl }}" alt="" class="h-10">
          @endif
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endif
