@section('intro')
    <div class="bd-intro ps-lg-4">
        <div class="d-md-flex flex-md-row align-items-center justify-content-between">
            <h1 class="bd-title mb-0" id="content">{{ $slot }}</h1>
            @push('title'){{ $slot }} @endpush
        </div>
        @if (isset($summary))
            <p class="bd-lead">{{ $summary }}</p>
        @endif
    </div>
@endsection
