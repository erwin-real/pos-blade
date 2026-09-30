<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
    public function boot(Request $request): void
    {
        // Check if the request is routed through ngrok
        if (str_ends_with($request->getHost(), '.ngrok-free.dev')) {
            // Force the asset and route URLs to use the ngrok domain
            URL::forceRootUrl($request->getSchemeAndHttpHost());
            URL::forceScheme('https');
        }
    }
}
