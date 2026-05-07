<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (! $locale && $request->user()) {
            $locale = $request->user()->locale;
        }

        if ($locale && in_array($locale, ['hr', 'en', 'de'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
