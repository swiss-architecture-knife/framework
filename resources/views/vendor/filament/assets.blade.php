@if (isset($json))
    <script>
        window.filamentData = @js($json)
    </script>
@endif

@foreach ($assets as $asset)
    @if (! $asset->isLoadedOnRequest())
        {{ $asset->getHtml() }}
    @endif
@endforeach

<style>
    :root {
        @foreach ($cssVariables ?? [] as $cssVariableName => $cssVariableValue)  -- {{ $cssVariableName }}: {{ $cssVariableValue }};
    @endforeach

    }
</style>
