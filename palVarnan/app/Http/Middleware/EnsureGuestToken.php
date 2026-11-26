<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookie('guest_token')) {
            $token = 'gt_' . Str::uuid()->toString();
            $request->cookies->set('guest_token', $token);
            Cookie::queue('guest_token', $token, 60 * 24 * 30); // 30 days
        }

        return $next($request);
    }
}
