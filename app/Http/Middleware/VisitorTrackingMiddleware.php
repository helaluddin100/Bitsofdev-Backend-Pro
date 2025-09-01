<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class VisitorTrackingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting: Allow 10 requests per minute per IP
        $key = 'visitor-tracking:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key, 60); // 1 minute window

        // Optional: Validate referrer domain (uncomment if you want to restrict to specific domains)
        // $referrer = $request->header('referer');
        // if ($referrer) {
        //     $allowedDomains = [
        //         'yourdomain.com',
        //         'www.yourdomain.com',
        //         'localhost',
        //         '127.0.0.1'
        //     ];
        //
        //     $referrerHost = parse_url($referrer, PHP_URL_HOST);
        //     if (!in_array($referrerHost, $allowedDomains)) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Invalid referrer domain'
        //         ], 403);
        //     }
        // }

        return $next($request);
    }
}
