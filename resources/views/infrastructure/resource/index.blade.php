@extends('swark::layout')

@section('content')
    <x-swark::intro>
        {{ __('swark::resource.detail.title', ['name' => $resource_type->name]) }}
    </x-swark::intro>

    <table class="table">
        <thead>
        <tr>
            <th>{{ __('swark::resource.table.cols.provider.title') }}</th>
            <th>{{ __('swark::resource.table.cols.resource.title') }}</th>
            <th>{{ __('swark::resource.table.cols.consumer.title') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($root as $provider)
            @if($provider['provider'])
                <tr>
                    <td colspan="3"><strong>{{ $provider['provider']['name'] }} @if($provider['provider']['stage'])
                                ({{ $provider['provider']['stage'] }})
                            @endif</strong></td>

                </tr>
            @endif
            @foreach($provider['items'] as $resource)
                <tr>
                    <td></td>
                    <td colspan="2">{{ $resource['resource']['name'] }}</td>
                </tr>
                @foreach ($resource['items'] as $consumer)
                    @if($consumer['consumer'])
                        <tr>
                            <td colspan="2"></td>
                            <td>{{ $consumer['consumer']['name'] }}</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>
@endsection
