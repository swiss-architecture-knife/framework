@extends('swark::layout')
@section('content')
    <x-swark::intro>
        {{ __('swark::g.infrastructure.baremetal.title') }}
    </x-swark::intro>

    <x-swark-outline :chapters="$page->chapters" />

    <x-swark-chapter :chapter="$page->chapters->pull()">
        {{-- @formatter:off --}}
    <x-swark-plantuml id="infrastructure/baremetal/overview">
left to right direction

@foreach ($root as $managed_service_provider)
    @if($managed_service_provider['msp'])
    cloud "{{ $managed_service_provider['msp']['name'] }}" {
    @endif
    @foreach ($managed_service_provider['items'] as $region)
        @if ($region['region'])
        frame "{{ $region['region']['name'] }}" {
        @endif

        @foreach ($region['items'] as $az)
            @if ($az['az'])
            frame "{{ $az['az']['name'] }}" {
            @endif

            @foreach ($az['items'] as $baremetal)
                node "{{ $baremetal['baremetal']['name'] }}" {

                    @if ($baremetal['host'])
                    label "{{ $baremetal['host']['name'] }}" as lbl_{{ $baremetal['host']['id'] }}
                    @endif

                    @if($baremetal['virtualizer'])
                    node "{{ $baremetal['virtualizer']['name'] }}" as bm_{{  $baremetal['virtualizer']['id'] }} {
                    @endif
                        @if ($baremetal['os'])
                        node "{{ $baremetal['os']['name'] }}" as node_os_{{ $baremetal['host']['id'] }}
                        @endif
                    @if($baremetal['virtualizer'])
                    }
                    @endif
                }
                @endforeach
            @if ($az['az'])
            }
            @endif
        @endforeach
        @if ($region['region'])
        }
        @endif
    @endforeach
    @if($managed_service_provider['msp'])
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
                <th>{{ __('swark::g.infrastructure.baremetal.table.cols.region.title') }}</th>
                <th>{{ __('swark::g.infrastructure.baremetal.table.cols.name.title') }}</th>
                <th>{{ __('swark::g.infrastructure.baremetal.table.cols.virtualizer.title') }}</th>
                <th>{{ __('swark::g.infrastructure.baremetal.table.cols.operating_system.title') }}</th>
                <th>{{ __('swark::g.infrastructure.baremetal.table.cols.hostname.title') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($root as $managed_service_provider)
                @if($managed_service_provider['msp'])
                    <tr>
                        <td colspan="5"><strong>{{ $managed_service_provider['msp']['name'] }}</strong></td>
                    </tr>
                @endif
                @foreach ($managed_service_provider['items'] as $region)
                    @if ($region['region'])
                        <tr>
                            <td colspan="5" class="px-2"><strong>{{ $region['region']['name'] }}</strong></td>
                        </tr>
                    @endif
                    @foreach ($region['items'] as $az)
                        @if ($az['az'])
                            <tr>
                                <td colspan="5" class="px-4"><strong>{{ $az['az']['name'] }}</strong></td>
                            </tr>
                        @endif
                        @foreach ($az['items'] as $baremetal)
                            <tr>
                                <td></td>
                                <td>{{ $baremetal['baremetal']['name'] }}</td>
                                <td>
                                    @if($baremetal['virtualizer'])
                                        {{ $baremetal['virtualizer']['name'] }}
                                    @endif
                                </td>
                                <td>
                                    @if ($baremetal['os'])
                                        {{ $baremetal['os']['name'] }}
                                    @endif
                                </td>
                                <td>
                                    @if ($baremetal['host'])
                                        {{ $baremetal['host']['name'] }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
            </tbody>
        </table>
    </x-swark-chapter>
@endsection
