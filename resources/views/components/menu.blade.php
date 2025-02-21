@foreach($items as $item)
    <li class="{{ $liCssClasses($item) }}">
        @if(sizeof($item['children']) > 0)
            <a class="{{ $aClasses($item) }}" href="@if (isset($item['url'])){{ $item['url'] }}@else#@endif"
                {{ $attrToggle($item) }}>{{ $item['title'] }}</a>
            <ul class="dropdown-menu">
                <x-swark-menu :items="$item['children']" />
            </ul>
        @else
            <a class="{{ $aClasses($item) }}" href="{{ $item['url'] }}">{{ $item['title'] }}</a>
        @endif
    </li>
@endforeach
