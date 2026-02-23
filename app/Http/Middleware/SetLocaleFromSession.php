<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? $this->localeFromAcceptLanguage($request);

        $locale = strtolower((string) $locale);

        if (in_array($locale, ['fr', 'en'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function localeFromAcceptLanguage(Request $request): ?string
    {
        $header = $request->header('Accept-Language');
        if (! $header) {
            return null;
        }
        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0]));
            if (in_array($tag, ['fr', 'en'], true)) {
                return $tag;
            }
            if (str_starts_with($tag, 'fr')) {
                return 'fr';
            }
            if (str_starts_with($tag, 'en')) {
                return 'en';
            }
        }
        return null;
    }
}

