<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        // after trusting proxies, $request->secure() will reflect original scheme
        if (!$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}
