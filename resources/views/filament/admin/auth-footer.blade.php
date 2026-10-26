{{-- Small note under the admin login form (AUTH_LOGIN_FORM_AFTER hook). --}}
<p class="fi-auth-footnote">
    <x-filament::icon icon="heroicon-m-lock-closed" class="fi-auth-footnote-icon" />
    {{ __('admin.login.footnote', ['year' => now()->year, 'site' => \App\Support\Branding::siteName()]) }}
</p>
