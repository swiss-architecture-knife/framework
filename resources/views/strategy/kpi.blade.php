@extends('swark::layout')
@section('content')
    <h2>{{ __('swark::g.kpi.title') }}</h2>
    <table class="table">
        @php($last_objective = null)

        <thead>
        <tr>
            <th>{{ __('swark::g.kpi.table.objective') }}</th>
            <th>{{ __('swark::g.kpi.table.target') }}</th>
            <th>{{ __('swark::g.kpi.table.current') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($kpis as $kpi)
            @if ($last_objective != $kpi->objective_name)
                <tr>
                    <td colspan="3"><strong>{{ $kpi->objective_name }}</strong></td>
                </tr>
                @php($last_objective = $kpi->objective_name)
            @endif

            <tr>
                <td class="px-4">@if ($kpi->action_name){{ $kpi->action_name }}: <br />@endif{{ $kpi->metric_name }}</td>
                <td>{{ $kpi->goal_value }}</td>
                <td><span class="badge bg-{{ $kpi->is_goal_reached ? 'success' : 'danger' }}">{{ $kpi->current_value }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
