@props(['section'])

{{-- Renders a page section with its type component (components/sections/{type}), or a basic fallback. --}}
@php($component = 'sections.'.str_replace('_', '-', $section->type))

@if (view()->exists('components.'.$component))
    <x-dynamic-component :component="$component" :section="$section" />
@else
    <x-sections.fallback :section="$section" />
@endif
