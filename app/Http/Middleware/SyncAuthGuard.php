<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncAuthGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('sanctum')->check() && !Auth::guard('web')->check()) {
            Auth::guard('web')->setUser(Auth::guard('sanctum')->user());
        }

        if (config('auth.defaults.guard') !== 'web') {
            config(['auth.defaults.guard' => 'web']);
        }

        return $next($request);
    }
}
