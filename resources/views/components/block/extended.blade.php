@props([
    'path' => null,
    'help' => null,
])
@if($help)
    <div class="alert alert-info">
        {{ $help }}
    </div>
@endif

@php($variants = $blocks->get($path))
@php($primary = $variants->first())

<div class="card my-4">
    <div class="card-body">
        @if ($variants->first() instanceof \Swark\Cms\Content\NotFound)
            <div class="bd-callout bd-callout-warning">
                Content für Pfad <code>{{ $path }}</code> wurde noch nicht hinterlegt.
            </div>
        @else
            {!! $variants->render() !!}
        @endif
    </div>

    <div class="card-footer">
        <div class="accordion" id="{{ $variants->unique('accordion-') }}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="{{ $variants->unique('heading-variants-') }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#{{ $variants->unique('collapse-versions-') }}"
                            aria-expanded="true" aria-controls="{{ $variants->unique('collapse-versions-') }}">
                        Versions
                    </button>
                </h2>
                <div id="{{ $variants->unique('collapse-versions-') }}" class="accordion-collapse collapse show"
                     aria-labelledby="{{ $variants->unique('heading-variants-') }}"
                     data-bs-parent="#{{ $variants->unique('accordion-') }}">
                    <div class="accordion-body">
                        @if ($variants->first()->hasChanges())
                            <ul>
                                @foreach($variants->first()->changes as $change)
                                    <li>{{ $change->createdAt}}
                                        : {{ empty($change->author) ? 'Kein Autor' : $change->author }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info">Keine Versionen vorhanden</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="{{ $variants->unique('heading-origins-') }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#{{ $variants->unique('collapse-origins-') }}" aria-expanded="false"
                            aria-controls="{{ $variants->unique('collapse-origins-') }}">
                        Alternative content for this block
                    </button>
                </h2>
                <div id="{{ $variants->unique('collapse-origins-') }}" class="accordion-collapse collapse"
                     aria-labelledby="{{ $variants->unique('heading-origins-') }}"
                     data-bs-parent="#{{ $variants->unique('accordion-') }}">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Path</th>
                            </tr>
                            @foreach ($variants as $variant)
                                @continue(!in_array($variant->source->type, ['database', 'fs']))
                                <tr>
                                    <td>{{ $variant->source->type }} </td>
                                    <td>{{ $variant->body->contentType }}</td>
                                    <td>@if ($variant == $variants->first())
                                            <span class="badge bg-success">active</span>
                                        @endif {{ $variant->source->logicalPath }} </td>
                                </tr>
                            @endforeach
                        </table>
                        @if ($variants->hasSuggestions())
                            <h3>How to to add content</h3>
                            @foreach ($variants->suggestions() as $suggestion)
                                {{ $suggestion }}
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
