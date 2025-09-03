@props([
  'routeName',
  'routeParams' => [],
  'pageDTO',
  'filters'     => [],
  'targetId',
  'formId'      => null,
])

@php
  $baseUrl = route($routeName, $routeParams);

  $makeQuery = function(int $page) use ($filters, $pageDTO) {
    return http_build_query(array_merge($filters, [
      'page'  => $page,
      'limit' => $pageDTO->limit,
    ]));
  };
@endphp

<nav class="mt-4">
  <ul class="inline-flex items-center space-x-1">

    @if($pageDTO->page > 1)
      <li>
        <a
          href="{{ $baseUrl }}?{{ $makeQuery($pageDTO->page - 1) }}"
          hx-get="{{ $baseUrl }}?{{ $makeQuery($pageDTO->page - 1) }}"
          @if($formId) hx-include="#{{ $formId }}" @endif
          hx-target="#{{ $targetId }}"
          hx-swap="innerHTML"
          class="px-2 hover:underline"
        >«</a>
      </li>
    @else
      <li class="px-2 text-gray-400">«</li>
    @endif

    @for($num = 1; $num <= ($pageDTO->totalPages ?? 1); $num++)
      @if($num === $pageDTO->page)
        <li class="px-2 font-bold">{{ $num }}</li>
      @else
        <li>
          <a
            href="{{ $baseUrl }}?{{ $makeQuery($num) }}"
            hx-get="{{ $baseUrl }}?{{ $makeQuery($num) }}"
            @if($formId) hx-include="#{{ $formId }}" @endif
            hx-target="#{{ $targetId }}"
            hx-swap="innerHTML"
            class="px-2 hover:underline"
          >{{ $num }}</a>
        </li>
      @endif
    @endfor

    @if(($pageDTO->totalPages ?? 1) > $pageDTO->page)
      <li>
        <a
          href="{{ $baseUrl }}?{{ $makeQuery($pageDTO->page + 1) }}"
          hx-get="{{ $baseUrl }}?{{ $makeQuery($pageDTO->page + 1) }}"
          @if($formId) hx-include="#{{ $formId }}" @endif
          hx-target="#{{ $targetId }}"
          hx-swap="innerHTML"
          class="px-2 hover:underline"
        >»</a>
      </li>
    @else
      <li class="px-2 text-gray-400">»</li>
    @endif

  </ul>
</nav>
