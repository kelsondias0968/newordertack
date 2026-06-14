<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var array<int, string>
     */
    protected array $supportedLocales = ['en', 'pt'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');

        if (is_string($locale) && in_array($locale, $this->supportedLocales, true)) {
            $request->session()->put('locale', $locale);
        }

        App::setLocale(
            $request->session()->get('locale', config('app.locale'))
        );

        return $next($request);
    }
}
