<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid API token.',
                'error' => 'Missing or invalid authentication token'
            ], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. You are not authorized to access this resource.',
                'error' => 'Unauthorized'
            ], 403);
        }
        return $next($request);
    }
}
