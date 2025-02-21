@extends('swark::layout')
@section('content')
    <x-swark::intro>
        {{ __('swark::g.infrastructure.landscape.title') }}
        <x-slot:summary>{{ __('swark::g.infrastructure.landscape.lead') }}</x-slot:summary>
    </x-swark::intro>

    {{-- no chapters here --}}
    <x-swark-plantuml id="~landscape" />
@endsection
