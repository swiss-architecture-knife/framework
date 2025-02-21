@extends('swark::layout')
@section('content')
    <x-swark::intro>
        {{ __('swark::software.title') }}
        <x-slot:summary>{{ __('swark::software.catalog.lead') }}</x-slot:summary>
    </x-swark::intro>


    {{-- software catalog --}}
    <table class="table">
        <thead>
        <tr>
            <th>{{ __('swark::software.catalog.table.cols.vendor.title')}}</th>
            <th>{{ __('swark::software.catalog.table.cols.name.title') }}</th>
            <th>{{ __('swark::software.catalog.table.cols.zone.title') }}</th>
            <th>{{ __('swark::software.catalog.table.cols.releases.title') }}</th>
            <th>{{ __('swark::software.catalog.table.cols.instances.title') }}</th>
            <th>{{ __('swark::software.catalog.table.cols.hosts.title') }}</th>
            <th>{{ __('swark::software.catalog.table.cols.runtimes.title') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($softwares as $cube)
            @if($cube['vendor'])
                <tr>
                    <td colspan="7"><strong>{{ $cube['vendor']['name'] }}</strong></td>
                </tr>
            @endif
            @forelse($cube['items'] as $software)
                <tr>
                    <td></td>
                    <td>{{ $software['software']['name'] }}
                        <br />
                        @if(isset($software['infrastructure_criticality']))
                        <x-swark::criticality :range="$criticality_range" :
                                       :position="$software['infrastructure_criticality']['position']"
                                       :name="$software['infrastructure_criticality']['name']"
                                        prefix="ical: "
                        />
                        @endif
                        @if(isset($software['business_criticality']))
                            <x-swark::criticality :range="$criticality_range" :
                                           :position="$software['business_criticality']['position']"
                                           :name="$software['business_criticality']['name']"
                                           prefix="bcal: "
                            />
                        @endif
                    </td>
                    <td>{{ $software['software']['zone_name'] }}</td>
                    <td>{{ $software['software']['total_releases'] }}</td>
                    <td>{{ $software['software']['total_application_instances'] }}</td>
                    <td>{{ $software['software']['total_hosts'] }}</td>
                    <td>{{ $software['software']['total_runtimes'] }}</td>
                </tr>
            @empty
                <x-swark::empty-table cols="7"></x-swark::empty-table>
            @endforelse
        @empty
            <x-swark::empty-table cols="7"/>
        @endforelse
        </tbody>
    </table>
@endsection
