@extends('swark::layout')
@section('content')
    <x-swark-outline :chapters="$page->chapters" />

    @foreach ($page->chapters->each() as $chapter)
        <x-swark-chapter :chapter="$chapter" />
    @endforeach
@endsection
