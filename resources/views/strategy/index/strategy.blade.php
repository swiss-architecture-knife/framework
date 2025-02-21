<p>
    <x-swark-content id="~introduction" default="swark::g.strategy.introduction.none" />
</p>

@if($strategy_latest)
    <x-swark-plantuml id="strategy/mindmap">
        @startmindmap
        * {{ $strategy_latest->name }}
        @foreach ($strategy_latest->objectives as $objective)
            ** {{ $objective->name }}
        @endforeach
        @endmindmap
    </x-swark-plantuml>

    @foreach ($strategy_latest->objectives as $objective)
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="card-body">
                        <h3 class="card-title">{{ $objective->name }}</h3>
                        <p class="lead">{{  $objective->displayDescription()->render() }}</p>

                        @if(!empty($objective->reason))
                            <p class="card-text"><strong>{{ __('swark::g.strategy.reason') }}</strong>
                                {{ $objective->display('reason') }}
                            </p>
                        @endif
                        <small class="text-body-secondary">
                            <a href="{{ route('swark.strategy.findings') }}#{{ $objective->id }}">{{ sizeof($findings_by_objective[$objective->id]->findings) }}
                                {{ __('swark::g.strategy.findings') }}</a>
                            <x-swark::scomp type="strategy" id="{{ $objective->scomp_id }}"/>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <x-swark::alert type="warning">{{ __('swark::g.strategy.none') }}</x-swark::alert>
@endif
