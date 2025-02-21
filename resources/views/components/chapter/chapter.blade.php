@props([
    'chapter' => null,
    'context' => null,
])
@action('chapter:before:splat', ['chapter' => $chapter, 'context' => $context])
{{--
Do *not* include this anonymous component directly. Instead use the Swark\Components\Block\Inline class as wrapper.
If we would do a $chapter->pull() it would be called twice and messes up the current pointer in the tree structure.
@see https://github.com/laravel/framework/issues/50777
--}}
<x-swark-chapter-header :chapter="$chapter" />

@action('chapter-body:before:splat', ['chapter' => $chapter, 'context' => $context])

@if(!$slot->isEmpty())
    {{ $slot }}
@else
    <x-swark-resolve :chapter="$chapter" />
@endif

@action('chapter-body:after:splat', ['chapter' => $chapter, 'context' => $context])
