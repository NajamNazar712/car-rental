@php($dir = config('locales.supported.'.app()->getLocale().'.dir', 'ltr'))
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteName ?? config('app.name'))</title>
    @hasSection('description')<meta name="description" content="@yield('description')">@endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Inter + Manrope: both cover Latin + Cyrillic --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    {{-- RTL FONT HOOK: when an rtl locale (e.g. Arabic) is enabled, push its font link onto the
         "rtl-fonts" stack (e.g. Noto Kufi Arabic) and override the fonts under [dir='rtl'] in resources/css/app.css. --}}
    @if ($dir === 'rtl')
        @stack('rtl-fonts')
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-surface font-sans text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2">
        {{ __('site.skip_to_content') }}
    </a>

    <header class="sticky top-0 z-40 border-b border-black/5 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="font-display text-xl font-extrabold text-brand">{{ $siteName ?? config('app.name') }}</a>
            <div class="flex items-center gap-3">
                <x-language-switcher />
                <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center rounded-lg bg-brand px-4 font-semibold text-white hover:bg-brand-dark">{{ __('site.book_now') }}</a>
            </div>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="border-t border-black/5 py-8 text-center text-sm text-ink-muted">
        &copy; {{ now()->year }} {{ $siteName ?? config('app.name') }}. {{ __('site.all_rights_reserved') }}
    </footer>
    @stack('scripts')
</body>
</html>
