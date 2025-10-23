<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Try both guards to ensure compatibility
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        
        if (!$user) {
            Log::warning('Authentication failed', [
                'token' => $request->bearerToken(),
                'sanctum_user' => Auth::guard('sanctum')->user(),
                'default_user' => Auth::user()
            ]);
            
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid API token.',
                'error' => 'Missing or invalid authentication token'
            ], 401);
        }
    
        // Check if the user is active
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Unauthorized. Your account is not active.',
                'error' => 'Account is not active'
            ], 403);
        }
        
        // **FIX: Set the user for the default guard**
        Auth::setUser($user);
    
        return $next($request);
    }
}