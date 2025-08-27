<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;


class AppLocalization
{
    public function handle(Request $request, Closure $next)
    {
        $defaultLanguage = config('app.locale');
        $usedLanguage = $request->header('Accept-Language', $defaultLanguage);
        if (! in_array($usedLanguage, config('core.available_locales'))) {
            $usedLanguage = $defaultLanguage;
        }
        app()->setLocale($usedLanguage);
        Carbon::setLocale($usedLanguage);

        return $next($request);
    }
}
