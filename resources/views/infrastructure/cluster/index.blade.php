@extends('swark::layout')

@section('content')
    <x-swark::intro>
        {{ __('swark::cluster.title') }}
    </x-swark::intro>
    <table class="table">
        <thead>
        <tr>
            <th></th>
            <th colspan="4" class="border-1">{{ __('swark::cluster.summary.table.cols.total.title') }}</th>
            <th></th>
        </tr>
        <tr>
            <th>{{ __('swark::cluster.summary.table.cols.name.title') }}</th>
            <th>{{ __('swark::cluster.summary.table.cols.baremetal.title') }}</th>
            <th>{{ __('swark::cluster.summary.table.cols.namespaces.title') }}</th>
            <th>{{ __('swark::cluster.summary.table.cols.runtimes.title') }}</th>
            <th>{{ __('swark::cluster.summary.table.cols.instances.title') }}</th>
            <th>{{ __('swark::cluster.summary.table.cols.release.title') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>
                    <a href="{{ route('swark.infrastructure.cluster.detail', [ $item->cluster_id ]) }}">{{ $item->cluster_name }}</a>
                </td>
                <td>{{ $item->cluster_total_baremetals }}</td>
                <td>{{ $item->cluster_total_namespaces }}</td>
                <td>{{ $item->cluster_total_runtimes }}</td>
                <td>{{ $item->cluster_total_application_instances }}</td>
                <td>@if($item->target_software_release_id)
                        {{ $item->target_software_name . ":" . $item->target_software_release_version }}
                    @else
                        ---
                    @endif</td>
            </tr>
        @empty
            <x-swark::empty-table cols="6"/>
        @endforelse
        </tbody>
    </table>
@endsection
