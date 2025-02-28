<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/swark/css/bootstrap-5.1.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/swark/css/bootstrap-docs-5.1.css') }}" rel="stylesheet"/>
    <script src="{{ asset('vendor/swark/js/plotly-2.31.1.min.js') }}" charset="utf-8"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.min.css" rel="stylesheet">
    @stack('header_js')

</head>
<body @hasSection('outline')data-bs-spy="scroll" data-bs-target="#TableOfContents"@endif>
<div class="d-block px-3 py-2 text-center text-bold skippy">
    <a href="https://dreitier.com/" class="text-white text-decoration-none">You are using an open source license of <em>swark</em>. Consider purchasing a proper support plan.</a>
</div>
@hasSection('outline')
    <div class="skippy visually-hidden-focusable overflow-hidden">
        <div class="container-xl">
            <a class="d-inline-flex p-2 m-1" href="#content">Skip to main content</a>
            <a class="d-none d-md-inline-flex p-2 m-1" href="#bd-docs-nav">Skip to docs navigation</a>
        </div>
    </div>
@endif
<header>

    <nav class="navbar navbar-expand-md navbar-dark bd-navbar">
        <div class="container-xxl flex-wrap flex-md-nowrap">
            <a class="navbar-brand" href="{{ route('swark.strategy.index') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="bdNavbar">
                <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav pt-2 py-md-0">
                    <x-swark-menu :items="$top_navigation"/>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container-xxl bd-gutter mt-3 my-md-4 bd-layout">
    <aside class="bd-sidebar">
        <nav class="collapse bd-links" id="bd-docs-nav" aria-label="Docs navigation">
            @include('swark::_partials.side_navigation_level', ['items' => $side_navigation, 'depth' => 0])
        </nav>
    </aside>
    <main class="bd-main order-1">
        @hasSection('intro')
            @yield('intro')
        @endif
        @hasSection('outline')
            @yield('outline')
        @endif
        <div class="bd-content ps-lg-4">
            @yield('content')
        </div>
    </main>
</div>
<div class="d-block px-3 py-2 text-center text-bold skippy">
    <a href="https://dreitier.com/" class="text-white text-decoration-none">powered by swark - architecture and strategy by dreitier GmbH</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
@stack('footer_js')
</body>
</html>
