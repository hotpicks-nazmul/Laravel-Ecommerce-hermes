<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install/*') || $request->is('install')) {
            return $next($request);
        }

        $locale = 'en';
        $isRTL = false;

        try {
            $localeFromSession = Session::get('locale');
            if (!$localeFromSession) {
                $defaultLanguage = \App\Models\Language::getDefault();
                $locale = $defaultLanguage ? $defaultLanguage->code : 'en';
                Session::put('locale', $locale);
            } else {
                $locale = $localeFromSession;
            }

            $currentLanguage = \App\Models\Language::where('code', $locale)->first();
            if ($currentLanguage) {
                $isRTL = $currentLanguage->is_rtl;
                view()->share('currentLanguage', $currentLanguage);
            }
        } catch (\Exception $e) {
            $locale = 'en';
            $isRTL = false;
        }

        App::setLocale($locale);
        view()->share('isRTL', $isRTL);

        return $next($request);
    }
}
