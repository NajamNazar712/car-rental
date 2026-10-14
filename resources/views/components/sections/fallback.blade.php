@props(['section'])

{{-- Generic section output; specific section components are built in Milestone 4. --}}
@if ($section->get('heading') || $section->get('subheading') || $section->get('body') || $section->get('text'))
    <section class="mx-auto max-w-7xl px-4 py-12" id="{{ $section->key }}">
        @if ($section->get('heading'))
            <h2 class="font-display text-3xl font-bold text-ink">{{ $section->get('heading') }}</h2>
        @endif
        @if ($section->get('subheading'))
            <p class="mt-2 text-lg text-ink-muted">{{ $section->get('subheading') }}</p>
        @endif
        @if ($section->get('text'))
            <p class="mt-4 text-ink-muted">{{ $section->get('text') }}</p>
        @endif
        @if ($section->get('body'))
            <div class="prose mt-4 max-w-none">{!! $section->get('body') !!}</div>
        @endif
    </section>
@endif
