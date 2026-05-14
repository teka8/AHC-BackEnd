<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Required headers for SecurityHeaders.com A-grade
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // HSTS (Strict-Transport-Security)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        
        // Permissions-Policy (replaces Feature-Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content-Security-Policy (CSP)
        // Kept slightly permissive to ensure your frontend/React works without breaking.
        $response->headers->set('Content-Security-Policy', "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; img-src 'self' data: https:; font-src 'self' data: https:;");

        return $response;
    }
}
