<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User; 
class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token is missing'], 401);
        }

        $user = User::where('api_token', $token)->first(); 

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }
}
