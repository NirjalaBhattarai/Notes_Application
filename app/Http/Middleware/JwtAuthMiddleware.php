<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();//look at the incomming request usually in auth bearer and extract token verify it 
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        }
        return $next($request);
    }
}

