<div class="row p-4 border border-info rounded border-1">
    @php($first = $diagram->first())
    @if ($output = $diagram->firstOutput())
        <a href="{{ $output->url }}"><img src='{{ $output->url }}' class="img-fluid"/></a>
    @else
        <div class="alert alert-danger">Unable to render PlantUML: {{ $diagram->firstError() ?? 'Unknown error' }}</div>
    @endif
</div>

<ul class="nav nav-pills">
    @foreach($diagram->sources() as $source)
        <li class="nav-item">
            <button class="btn @if($first && $first->id() == $source->id()) btn-info @else btn-outline-info @endif mt-3 mx-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#{{ $source->id() }}"
                    aria-expanded="false"
                    aria-controls="{{ $source->id() }}">Show PlantUML code ({{ $source->description }})
            </button>
        </li>
    @endforeach
</ul>
@foreach($diagram->sources() as $source)
    <div class="row justify-content-md-center my-4">
        <div class="col-12">
            <div class="collapse multi-collapse" id="{{ $source->id() }}">
                @if($source->path)
                <x-swark::alert>Diagram read from {{ $source->path }}</x-swark::alert>
                @endif
<pre><code class="language-promql">
{{ $source->content }}
</code></pre>
            </div>
        </div>
    </div>
@endforeach
