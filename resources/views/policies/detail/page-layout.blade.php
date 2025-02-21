@extends('swark::layout')
@section('content')
    <x-swark::intro>
        {{ $policy->name }}
        <x-slot:summary>{{  $policy->displayDescription()->render() }}</x-slot:summary>
    </x-swark::intro>

    <x-swark-outline :chapters="$page->chapters" />

    <x-swark-chapter :chapter="$page->chapters->pull()">
        @foreach($policy->rules as $rule)
            <x-swark-chapter :chapter="$page->chapters->pull()">
                {!!  $rule->description !!}
            </x-swark-chapter>
        @endforeach
    </x-swark-chapter>
@endsection
