<?php

namespace App\Providers;

use App\Content\SectionTypeRegistry;
use App\Policies\RolePolicy;
use App\Support\Branding;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelSettings\Events\SettingsSaved;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SectionTypeRegistry::class);
    }

    public function boot(): void
    {
        // Vendor models are outside policy auto-discovery.
        Gate::policy(Role::class, RolePolicy::class);

        // Audit trail for settings changes (spatie/laravel-settings refreshes its own cache on save).
        Event::listen(SettingsSaved::class, function (SettingsSaved $event): void {
            activity('settings')
                ->causedBy(auth()->user())
                ->withProperties(['group' => $event->settings::group(), 'attributes' => $this->redact($event->settings->toArray())])
                ->log('updated');
        });

        View::composer('layouts.app', function ($view): void {
            $view->with('siteName', Branding::siteName());
        });
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function redact(array $values): array
    {
        foreach ($values as $key => $value) {
            if (str_contains($key, 'secret') && filled($value)) {
                $values[$key] = '********';
            }
        }

        return $values;
    }
}
