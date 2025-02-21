@extends('swark::layout')
@section('content')
    <x-swark-outline :chapters="$page->chapters"/>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        <x-swark-plotly width="800" height="650">
            [{
            type: 'scatterpolar',
            r: {!! json_encode(collect($findings_by_objective)->map(fn($item) => collect($item->findings)->count())->values()->toArray()) !!}
            ,
            theta: {!! json_encode(collect($findings_by_objective)->map(fn($item) => $item->name)->values()->toArray()) !!}
            ,
            fill: 'toself'
            }]
            <x-slot:options>
                {
                polar: {
                radialaxis: {
                visible: true,
                range: [0, 15]
                }
                },
                showlegend: false
                }
            </x-slot:options>
            <x-slot:config>{responsive:true}</x-slot:config>
        </x-swark-plotly>

        @forelse($findings_by_objective as $item)
            <x-swark-chapter :chapter="$page->chapters->pull()">
                @if (sizeof($item->findings) > 0)
                    <ul>
                        @foreach($item->findings as $finding)
                            <li><a href="#finding-{{ $finding->id }}">{{ $finding->name }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <x-swark::alert>{{ __('swark::g.findings.none') }}</x-swark::alert>
                @endif
            </x-swark-chapter>
        @empty
            <x-swark::alert>{{ __('swark::g.findings.none') }}</x-swark::alert>
        @endforelse
        {{-- After we have iterated over the inline ToC, we have to skip those number of elements in the ToC on the right side --}}
    </x-swark-chapter>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        @foreach ($findings as $finding)
            <h3 id="finding-{{ $finding->id }}">{{ $finding->name }}</h3>
            {{  $finding->description->render() }}

            @if (sizeof($finding->actions) > 0 )
                @foreach ($finding->actions as $action)
                    <h5>{{ $action->name }}</h5>
                    {{ $action->description->render() }}
                @endforeach
            @endif

            <x-swark::criticality :range="$criticality_range" :position="$finding->criticality->position"
                                  :name="$finding->criticality->name"/>

        <x-swark::status :status="$finding->status" />

        @endforeach
    </x-swark-chapter>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        <table class="table">
            <thead>
            <tr>
                <th> {{ __('swark::g.findings.table.cols.action.title') }}</th>
                <th> {{ __('swark::g.findings.table.cols.status.title')}}</th>
                <th> {{ __('swark::g.findings.table.cols.begin_at.title')}}</th>
                <th> {{ __('swark::g.findings.table.cols.end_at.title')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach(collect($findings)->values()->flatMap(function(object $array) { return $array->actions; })->sortBy(['begin_at', 'asc']) as $action)
                <tr>
                    <td>{{ $action->name }}</td>
                    <td>
                        <x-swark::status :status="$action->status" />
                    </td>
                    <td>
                        <x-swark::date :date="$action->begin_at" />
                    </td>
                    <td>
                        <x-swark::date :date="$action->end_at" />
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-swark-chapter>
@endsection
