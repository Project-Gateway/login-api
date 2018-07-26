<?php

namespace App\Http\Middleware;

use Closure;

class CacheResponse
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param int $expiresIn 10 minutes by default
     * @return mixed
     */
    public function handle($request, Closure $next, $type = 'public', $expiresIn = 600)
    {
        $response = $next($request);
        $response->header('Cache-control', "$type, max-age=$expiresIn");
        return $response;
    }
}
