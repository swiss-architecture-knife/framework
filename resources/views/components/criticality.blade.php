@props([
    'prefix' => '',
    'value' => 'low',
    ])
<span class="badge @if($value == 'low') bg-info @elseif($value == 'medium') bg-warning @else bg-danger @endif">{{$prefix ? $prefix . ':' : ''}}{{ $value }}</span>
