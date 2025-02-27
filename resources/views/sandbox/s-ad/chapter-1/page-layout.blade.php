@extends('swark::layout')
@section('content')
    <x-swark-outline :chapters="$page->chapters" />

    <h1>{{ $page->__('headline', 'Einleitung und Ziele') }}</h1>

    @foreach ($page->chapters->each() as $chapter)
        <x-swark-chapter :chapter="$chapter"/>
    @endforeach
@endsection
