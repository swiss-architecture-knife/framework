@extends('swark::layout')
@section('content')

    <x-swark::intro>
        Glossar
        <x-slot:summary>Allgemein genutzte Begriffe</x-slot:summary>
    </x-swark::intro>

    <x-swark-outline :chapters="$page->chapters"/>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        @include('swark::glossary.swark')

    </x-swark-chapter>

    <x-swark-chapter :chapter="$page->chapters->pull()">
        @include('swark::glossary.nis2')
    </x-swark-chapter>
@endsection
