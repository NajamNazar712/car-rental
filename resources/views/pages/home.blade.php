@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-24 text-center">
        <h1 class="font-display text-4xl font-extrabold text-ink">{{ config('app.name') }}</h1>
        <p class="mt-4 text-lg text-ink-muted">{{ __('site.coming_soon') }}</p>
    </section>
@endsection
