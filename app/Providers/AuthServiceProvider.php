<?php

namespace App\Providers;

use App\Services\Auth\AuthManager;
use App\Services\Auth\Contracts\AuthManagerContract;
use App\Services\Auth\Contracts\StorageContract;
use App\Services\Auth\Contracts\WhitelistContract;
use App\Services\Auth\Storage;
use App\Services\Auth\Whitelist;
use App\Services\Jwt\Contracts\TokenFactoryContract;
use App\Services\Jwt\TokenFactory;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TokenFactoryContract::class, TokenFactory::class);
        $this->app->singleton(AuthManagerContract::class, AuthManager::class);
        $this->app->singleton(WhitelistContract::class, Whitelist::class);
        $this->app->singleton(StorageContract::class, function ($app) {
            return $app->make(Storage::class, ['tags' => ['jwt']]);
        });
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function (Request $request) {

            // The Authorization header exists?
            if (!($authHeader = $request->header('authorization'))) {
                return null;
            }

            // extract the token from header
            $token = trim(preg_replace('/^\s*bearer/i', '', $authHeader));

            // parses the token
            if (!($tokenObject = $this->app->make(TokenFactoryContract::class)->parse($token))) {
                return null;
            }

            // check if it is whitelisted
            $authManager = $this->app->make(AuthManagerContract::class);
            if (!$authManager->check($tokenObject)) {
                return null;
            }

            $authManager->setToken($tokenObject);

            return $tokenObject;
        });
    }
}
