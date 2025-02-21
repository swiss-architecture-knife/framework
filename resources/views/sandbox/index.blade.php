@extends('swark::layout')
@section('content')
    <h1>It does not work</h1>
    <h1>Plantuml</h1>
    <h2>Inline network diagram</h2>
    <x-swark-plantuml id="bla">
        nwdiag {
        network dmz {
        address = "210.x.x.x/24"

        @if(1 == 1)
            web01 [address = "210.x.x.1"];
        @endif
        web02 [address = "210.x.x.2"];
        }
        }
    </x-swark-plantuml>

    <h2>
        Loading from a local file
    </h2>
    <x-swark-plantuml caching="true" id="sandbox/sample/c4_sample" extension="plantuml">
    </x-swark-plantuml>

    <h1>Mermaidjs</h1>
    <x-swark-mermaid-js>
        sankey-beta

        %% source,target,value
        Electricity grid,Over generation / exports,104.453
        Electricity grid,Heating and cooling - homes,113.726
        Electricity grid,H2 conversion,27.14
    </x-swark-mermaid-js>
    </p>
@endsection
