<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Missing authentication token'
            ], 401);
        }

        // Find token
        $apiToken = ApiToken::where('token', hash('sha256', $token))->first();

        if (!$apiToken || !$apiToken->isValid()) {
            return response()->json([
                'error' => 'Invalid or expired token'
            ], 401);
        }

        // Update last used
        $apiToken->update(['last_used_at' => now()]);

        // Attach user to request
        $request->merge(['user' => $apiToken->user]);
        $request->setUserResolver(function () use ($apiToken) {
            return $apiToken->user;
        });

        return $next($request);
    }
}