@if($exports->isEmpty())
  <p class="text-gray-600">Sin datos</p>
@else
  <ul class="space-y-2">
    @foreach($exports as $e)
      <li class="flex justify-between items-center border p-2 rounded">
        <div>
          <strong>{{ ucfirst($e->type->value) }}</strong> —
          {{ $e->status->value }}
          @if($e->status->value === 'done')
            ({{ $e->finished_at->format('Y-m-d H:i') }})
          @endif
        </div>
        <div>
          @if($e->status->value === 'done')
            <a href="{{ route('exports.download', $e) }}"
               class="text-indigo-600 underline">Descargar</a>
          @endif
        </div>
      </li>
    @endforeach
  </ul>
@endif
