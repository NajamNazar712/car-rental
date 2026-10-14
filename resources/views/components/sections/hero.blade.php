@props(['section'])

{{-- Basic hero (first slide). The full slider is built in Milestone 4. --}}
@php
    $slide = $section->get('slides.0', []);
    $image = $section->getFirstMedia('images');
@endphp

<section class="relative isolate overflow-hidden bg-brand-dark text-white">
    @if ($image)
        <img src="{{ $image->getUrl('large') }}" alt="{{ $image->getCustomProperty('alt', $slide['heading'] ?? '') }}"
             class="absolute inset-0 -z-10 h-full w-full object-cover opacity-50" fetchpriority="high">
    @endif
    <div class="mx-auto max-w-7xl px-4 py-24 sm:py-32">
        <h1 class="max-w-2xl font-display text-4xl font-extrabold sm:text-5xl">{{ $slide['heading'] ?? '' }}</h1>
        @if (! empty($slide['subheading']))
            <p class="mt-4 max-w-xl text-lg text-white/90">{{ $slide['subheading'] }}</p>
        @endif
    </div>
</section>
