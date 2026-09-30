<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class TrustNgrok
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Str::endsWith($request->getHost(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
        return $next($request);
    }
}