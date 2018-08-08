<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('echo/{value}', function ($value) {
    return "echo $value";
});

$router->post('auth/login', 'AuthController@login');
$router->get('auth/{provider}/url', 'AuthController@providerUrl');
$router->get('auth/login/{provider}', 'AuthController@providerCallback');
$router->group(['middleware' => 'cache:public,86400'], function() use ($router) {
    $router->get('auth/social/urls', 'AuthController@providerUrls');
});

// authenticated routes
$router->group(['middleware' => \App\Http\Middleware\Authenticate::class], function() use ($router) {
    $router->post('auth/logout', 'AuthController@logout');
    $router->post('auth/refresh', 'AuthController@refresh');
    $router->get('auth/me', 'AuthController@me');
    $router->get('auth/validate', 'AuthController@validateToken');
});
