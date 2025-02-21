@extends('swark::layout')

@section('content')
    <x-swark::intro>
        {{ __('swark::cluster.detail.title', ['name' => $cluster->name]) }}
    </x-swark::intro>
    <x-swark-chapter :chapter="$page->chapters->pull()">
        {{-- @formatter:off --}}
<x-swark-plantuml id="ffff">
left to right direction
@foreach ($application_instance_items as $namespace)
@if ($namespace['namespace'])
frame "{{ $namespace['namespace']['name'] }}" as {{ $namespace['namespace']['__cnt'] }}{
@endif

    @foreach ($namespace['items'] as $software)
    @if($software['runtime'] || $software['host'])
    frame "{{$software['runtime'] ? $software['runtime']['name'] : $software['host']['name'] }}" {
    @endif

        component "{{ $software['software']['name'] }}:{{ $software['release']['version'] }}" as {{ $software['software']['__cnt'] }}
    @if($software['runtime'] || $software['host'])
    }
    @endif
    @endforeach

@if ($namespace['namespace'])
}
@endif
@endforeach
</x-swark-plantuml>
{{-- @formatter:on --}}
    </x-swark-chapter>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        <table class="table">
            <thead>
            <tr>
                <th>{{ __('swark::application_instance.table.cols.namespace.title') }}</th>
                <th>{{ __('swark::application_instance.table.cols.software.title') }}</th>
                <th>{{ __('swark::application_instance.table.cols.stage.title') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($application_instance_items as $namespace)
                @if ($namespace['namespace'])
                    <tr>
                        <td colspan="3"><strong>{{ $namespace['namespace']['name'] }}</strong></td>
                    </tr>
                @endif
                @foreach ($namespace['items'] as $software)
                    <tr>
                        @if($software['runtime'] || $software['host'])
                            <td class="px-4">{{ $software['runtime'] ? $software['runtime']['name'] : $software['host']['name'] }}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $software['software']['name'] }}:{{ $software['release']['version'] }}</td>
                        <td>@if ($software['stage'])
                                {{ $software['stage']['name'] }}
                            @endif</td>
                    </tr>
                @endforeach
            @empty
                <x-swark::empty-table cols="3"/>
            @endforelse
            </tbody>
        </table>
    </x-swark-chapter>
@endsection
