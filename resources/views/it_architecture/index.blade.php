@extends('swark::layout')

@section('content')
    <x-swark::intro>
        {{ __('swark::g.it_architecture.title') }}
        <x-slot:summary>{{ __('swark::g.it_architecture.lead') }}</x-slot:summary>
    </x-swark::intro>

    <x-swark-outline :chapters="$page->chapters"/>

    {{-- data classification --}}
    <x-swark-chapter :chapter="$page->chapters->pull()">

        <table class="table">
            <thead>
            <tr>
                <th>{{ __('swark::g.it_architecture.data_classification.table.cols.class.title') }}</th>
                <th>{{ __('swark::g.it_architecture.data_classification.table.cols.description.title') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($data_classifications as $model)
                <tr>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->description }}</td>
                </tr>
            @empty
                <x-swark::empty-table cols="2"/>
            @endforelse
            </tbody>
        </table>
    </x-swark-chapter>

    {{-- zone model --}}
    <x-swark-chapter :chapter="$page->chapters->pull()">

        <table class="table">
            <thead>
            <tr>
                <th>{{ __('swark::g.it_architecture.zone_model.table.cols.zone.title') }}</th>
                <th>{{ __('swark::g.it_architecture.zone_model.table.cols.description.title') }}</th>
                <th>{{ __('swark::g.it_architecture.zone_model.table.cols.classification.title') }}</th>
                <th>{{ __('swark::g.it_architecture.zone_model.table.cols.actors.title') }}</th>
                <th>{{ __('swark::g.it_architecture.zone_model.table.cols.rules.title') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($zone_model as $model)
                <tr>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->description }}</td>
                    <td>{{ $model->dataClassification?->name }}</td>
                    <td>{{ $model->actors?->implode(function (object $item, int $key) {
                    return $item->name;
}, ', ') }}</td>
                    <td>{{ $model->rules()->get()->implode(fn($item) => $item->name, ',') }}</td>
                </tr>
            @empty
                <x-swark::empty-table cols="5"/>
            @endforelse
            </tbody>
        </table>

        <x-swark-plantuml id="zone-model">
            {!!  $c4_zone->create() !!}
        </x-swark-plantuml>
    </x-swark-chapter>

    {{-- zone matrix --}}
    <x-swark-chapter :chapter="$page->chapters->pull()">

        <x-swark::alert>
            {{  _t('swark::g.it_architecture.matrix.hint') }}
        </x-swark::alert>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>{{ _t('swark::g.it_architecture.matrix.table.cols.map.title') }}</th>
                @foreach ($matrix as $zone)
                    <th style="writing-mode: vertical-lr">{{ $zone->name }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($matrix as $zone)
                <tr>
                    <th scope="row">{{ $zone->name }}</th>
                    @foreach ($matrix as $zone_joined)
                        @if($zone_joined->name == $zone->name)
                            <td class="bg-secondary">&nbsp;</td>
                        @else
                            <td>
                                @if(in_array($zone_joined->name, $zone->allowed))
                                    <span class="badge bg-success">✓</span>
                                @elseif(in_array($zone_joined->name, $zone->denied))
                                    <span class="badge bg-danger">x</span>
                                @else
                                    <span class="badge bg-warning opacity-25"
                                          title="denied by default without explicit rule">x</span>
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-swark-chapter>
@endsection
