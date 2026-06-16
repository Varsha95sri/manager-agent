<?php
// app/Http/Middleware/ValidateApiKey.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('request_start_time', microtime(true));

        $apiKey = $request->header('x-api-key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'Unauthorized: API key is missing.'
            ], 401);
        }

        $hash = hash('sha256', $apiKey);

        $keyRecord = ApiKey::where('key_hash', $hash)
            ->where('is_active', true)
            ->first();

        if (!$keyRecord) {
            return response()->json([
                'message' => 'Unauthorized: Invalid API key.'
            ], 401);
        }

        // Store variables for the terminate method logging
        $request->attributes->set('api_key_id', $keyRecord->id);
        $request->attributes->set('api_key_user_id', $keyRecord->user_id);

        // Update last used at timestamp
        $keyRecord->update([
            'last_used_at' => now()
        ]);

        // Authenticate the user associated with this key for the request duration
        Auth::login($keyRecord->user);

        return $next($request);
    }

    /**
     * Perform any tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $keyId = $request->attributes->get('api_key_id');
        $userId = $request->attributes->get('api_key_user_id');

        if ($keyId && $userId) {
            $startTime = $request->attributes->get('request_start_time');
            $responseTimeMs = $startTime ? (int) round((microtime(true) - $startTime) * 1000) : 0;

            \Illuminate\Support\Facades\DB::table('api_logs')->insert([
                'user_id' => $userId,
                'api_key_id' => $keyId,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => $responseTimeMs,
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
