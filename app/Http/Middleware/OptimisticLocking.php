<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class OptimisticLocking
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $userId = $request->route('id');

            if ($userId) {
                $user = User::find($userId);
                $requestVersion = $request->header('X-Version')
                    ?? $request->input('version');

                if ($user && $requestVersion !== null && $user->version != $requestVersion) {
                    return response()->json([
                        'error' => 'Resource has been modified by another request',
                        'current_version' => $user->version,
                    ], 409);
                }
            }
        }

        return $next($request);
    }
}
