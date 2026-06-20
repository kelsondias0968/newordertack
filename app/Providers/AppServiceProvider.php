<?php

namespace App\Providers;

use App\Mail\ResendTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Mail::extend('resend-http', fn () => new ResendTransport(
            (string) config('services.resend.key')
        ));
    }
}
