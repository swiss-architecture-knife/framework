@section('outline')
    @if(!$slot->isEmpty() || (isset($chapters) && !$chapters->isEmpty()))
    <div class="bd-toc mt-4 mb-5 my-md-0 ps-xl-3 mb-lg-5 text-muted">
        <button class="btn btn-link p-md-0 mb-2 mb-md-0 text-decoration-none bd-toc-toggle d-md-none"
                type="button"
                data-bs-toggle="collapse" data-bs-target="#tocContents" aria-expanded="false"
                aria-controls="tocContents">
            {{ __('swark::g.toc.on_this_page') }}
            <svg class="bi d-md-none ms-2" aria-hidden="true">
                <use xlink:href="#chevron-expand"></use>
            </svg>
        </button>
        <strong class="d-block h6 my-2 pb-2 border-bottom">{{ __('swark::g.toc.on_this_page') }}</strong>
        <nav id="TableOfContents">
            <ul>
                @if(isset($chapters) && is_object($chapters))
                    @include('swark::components.outline._partials.node', ['item' => $chapters->rootItem()])
                @else
                    {{ $slot }}
                @endif
            </ul>
        </nav>
    </div>
    @endif
@endsection
