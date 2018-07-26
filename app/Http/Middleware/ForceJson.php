<?php

namespace App\Http\Middleware;

use Closure;

class ForceJson
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('Accept') || $request->header('Accept') == '*/*') {
            $request->headers->set('Accept', 'application/json');
        }
        return $next($request);
    }
}
