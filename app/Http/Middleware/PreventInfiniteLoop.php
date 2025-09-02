<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PreventInfiniteLoop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to AI response endpoint
        if ($request->is('api/chat/ai-response')) {
            $ip = $request->ip();
            $question = $request->input('question', '');

            // Create a unique key for this request
            $key = 'ai_request_' . md5($ip . $question);

            // Check if this exact request was made recently (within 5 seconds)
            if (Cache::has($key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait a moment before asking the same question again.',
                    'suggestion' => 'Try rephrasing your question or contact our support team for immediate assistance.',
                    'contact_link' => config('app.url') . '/contact'
                ], 429);
            }

            // Store this request for 5 seconds
            Cache::put($key, true, 5);
        }

        return $next($request);
    }
}
