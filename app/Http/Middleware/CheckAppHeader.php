<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Services\Auth\Contracts\AuthManagerContract;
use Closure;

class CheckAppHeader
{

    protected $authManager;

    public function __construct(AuthManagerContract $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $appName = $request->header(config('auth.applicationHeader'));

        if (!$appName) {
            return response(['message' => 'Application not defined'], 404);
        }

        if (!Application::byName($appName)->count()) {
            return response(['message' => 'Invalid Application'], 404);
        }

        $this->authManager->setApplication($appName);

        return $next($request);
    }
}
