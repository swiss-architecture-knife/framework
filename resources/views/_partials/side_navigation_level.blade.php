<ul class="list-unstyled @if($depth == 0) mb-0 py-3 pt-md-1 @else  fw-normal pb-1 small @endif">
    @foreach ($items as $item)
        <li class="mb-1">
            @if(!empty($item['children']))
                <button class="btn @if($depth > 0) btn-sm px-4 @endif d-inline-flex align-items-center rounded" data-bs-toggle="collapse"
                        data-bs-target="#{{ sha1($item['title']) }}" aria-expanded="true" aria-current="true">
                    {{ $item['title'] }}
                </button>

                <div class="collapse @if($depth > 0) px-2 @endif @if($item['active']) show @endif" id="{{ sha1($item['title']) }}">
                    @include('swark::_partials.side_navigation_level', ['items' => $item['children'], 'depth' => $item['depth'] + 1])
                </div>
        </li>
        @else
            <li><a href="{{ $item['url'] }}"
                   class="d-inline-flex align-items-center rounded @if($item['active']) active @endif"
                   aria-current="page">{{ $item['title'] }}</a></li>
        @endif
    @endforeach
</ul>
