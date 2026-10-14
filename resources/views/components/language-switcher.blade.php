@php($locales = config('locales.supported'))
@if (count($locales) > 1)
    <nav aria-label="{{ __('site.language') }}" class="flex items-center gap-2">
        @foreach ($locales as $code => $meta)
            <a href="{{ url($code) }}" hreflang="{{ $code }}" lang="{{ $code }}"
               @class(['rounded px-2 py-1 text-sm', 'font-semibold underline' => $code === app()->getLocale()])>
                {{ $meta['name'] }}
            </a>
        @endforeach
    </nav>
@endif
