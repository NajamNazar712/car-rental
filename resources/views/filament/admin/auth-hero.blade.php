{{--
    Left-hand panel on the admin login / password reset screens (SIMPLE_LAYOUT_START hook).
    Default: animated self-drawing car outline. To use a photo instead, add public/images/admin/auth-hero.jpg.
--}}
@php
    $photo = file_exists(public_path('images/admin/auth-hero.jpg')) ? asset('images/admin/auth-hero.jpg') : null;
@endphp

<aside class="fi-auth-hero" @if ($photo) style="background-image: url('{{ $photo }}')" @endif aria-hidden="true">
    @unless ($photo)
        @include('filament.admin.auth-hero-art')
    @endunless

    <div class="fi-auth-hero-content">
        <p class="fi-auth-hero-brand">{{ \App\Support\Branding::siteName() }}</p>
        <h2 class="fi-auth-hero-title">{{ __('admin.login.hero_title') }}</h2>
        <p class="fi-auth-hero-text">{{ __('admin.login.hero_text') }}</p>
    </div>
</aside>
