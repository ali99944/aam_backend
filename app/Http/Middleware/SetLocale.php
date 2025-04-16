<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request; // Add Request
use Illuminate\Support\Facades\Cache; // Import Cache
use Illuminate\Support\Str;

class SetLocale
{
    public function handle(Request $request, Closure $next) // Add Request type hint
    {
        // --- Get Available Locales (Cached) ---
        $availableLocales = Cache::remember('active_locales', now()->addHour(), function () {
            // Fetch only active locales
            return Language::where('is_active', true)->pluck('locale')->toArray();
        });
        // Ensure fallback is always available even if not in DB/active
        $fallbackLocale = config('app.fallback_locale', 'en');
        if (!in_array($fallbackLocale, $availableLocales)) {
            $availableLocales[] = $fallbackLocale;
        }
        // ---------------------------------------

        $sessionLocale = session('locale'); // Check session first
        $headerLocale = null;

        if ($request->hasHeader('Accept-Language')) {
            $rawLocale = $request->header('Accept-Language');
            $headerLocale = $this->parseLocale($rawLocale, $availableLocales, $fallbackLocale);
        }

        // Determine final locale: Session > Header > Fallback
        $locale = $sessionLocale ?? $headerLocale ?? $fallbackLocale;

        // Validate the determined locale against available ones
        if (!in_array($locale, $availableLocales)) {
             $locale = $fallbackLocale;
        }

        // Set the application locale
        app()->setLocale($locale);

        // Optionally: Persist the determined locale back to session if it wasn't set
        // if (!$sessionLocale || $sessionLocale !== $locale) {
        //     session(['locale' => $locale]);
        // }

        return $next($request);
    }

    /**
     * Parse the raw Accept-Language header value.
     */
    protected function parseLocale($rawLocale, array $availableLocales, string $fallbackLocale): string
    {
        try {
             // Use Intl extension for robust parsing if available
            if (extension_loaded('intl')) {
                $parsedLocale = \Locale::acceptFromHttp($rawLocale);
                 if ($parsedLocale) {
                     $locale = str_replace('-', '_', $parsedLocale); // en-US -> en_US
                     // Check full locale (e.g., en_US)
                     if (in_array($locale, $availableLocales)) return $locale;
                     // Check language part only (e.g., en)
                     if (strpos($locale, '_') !== false) {
                         $langPart = explode('_', $locale)[0];
                          if (in_array($langPart, $availableLocales)) return $langPart;
                     }
                     // If only language was parsed (e.g., 'en')
                      if (in_array($locale, $availableLocales)) return $locale;
                 }
            }
        } catch (\Exception $e) {
             // Fallback to basic parsing if intl fails or is not available
        }

        // Basic fallback parsing
        preg_match_all('/([a-z]{1,8}(?:[-_][a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $rawLocale, $matches);

        if (count($matches[1])) {
            $accepted = array_combine($matches[1], $matches[2]);
            arsort($accepted); // Sort by quality factor

            foreach ($accepted as $locale => $q) {
                $locale = str_replace('-', '_', $locale);
                if (in_array($locale, $availableLocales)) return $locale; // Match full locale e.g. en_US
                if (strpos($locale, '_') !== false) {
                    $langPart = explode('_', $locale)[0];
                     if (in_array($langPart, $availableLocales)) return $langPart; // Match base lang e.g. en
                }
            }
        }

        return $fallbackLocale; // Default if nothing matches
    }
}