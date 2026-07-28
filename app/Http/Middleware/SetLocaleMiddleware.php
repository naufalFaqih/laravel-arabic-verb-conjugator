<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', config('app.locale', 'id'));

        if (in_array($locale, ['id', 'en'])) {
            App::setLocale($locale);
        } else {
            App::setLocale(config('app.fallback_locale', 'en'));
        }

        return $next($request);
    }
}
