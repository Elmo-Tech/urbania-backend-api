<?php

namespace App\Providers;

use App\Mail\Transport\MicrosoftGraphTransport;
use App\Services\Microsoft\GraphMailClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('microsoft_graph', function () {
            return new MicrosoftGraphTransport(new GraphMailClient(
                config('services.microsoft_graph_mail'),
                config('mail.from')
            ));
        });
    }
}
