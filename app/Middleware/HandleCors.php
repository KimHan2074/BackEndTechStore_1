<?php

namespace App\Http\Middleware;

use Closure;

class HandleCors
{
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() === "OPTIONS") {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', 'https://frontendreacttechstore-production.up.railway.app/')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        $response = $next($request);

        return $response
            ->header('Access-Control-Allow-Origin', 'https://frontendreacttechstore-production.up.railway.app/')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
