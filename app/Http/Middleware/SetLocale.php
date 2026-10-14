<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');

        abort_unless(array_key_exists($locale, config('locales.supported')), 404);

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Keep the locale out of controller action parameters.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
