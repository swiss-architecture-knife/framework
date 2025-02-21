@foreach($item->items() as $child)
    <li><a href="#{{$child->uid()}}"><x:swark-label :chapter="$child" context="toc" /></a>
        @if($child->hasChildren())
            <ul>
                @include('swark::components.outline._partials.node', ['item' => $child])
            </ul>
        @endif
    </li>
@endforeach
