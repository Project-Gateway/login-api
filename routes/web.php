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
$router->post('auth/register', 'AuthController@register');
$router->group(['middleware' => 'cache:public,86400'], function() use ($router) {
    $router->get('auth/social/urls', 'AuthController@providerUrls');
});

// authenticated routes
$router->group(['middleware' => 'auth'], function() use ($router) {

    $router->post('auth/logout', 'AuthController@logout');
    $router->post('auth/refresh', 'AuthController@refresh');
    $router->get('auth/validate', 'AuthController@validateToken');

    $uuidRegex = '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}';

    // User CRUD routes
    $router->get('users', ['uses' => 'UserController@index', 'as' => 'users.index']);
    $router->get('users/paginated', ['uses' => 'UserController@indexPaginated', 'as' => 'users.indexPaginated']);
    $router->get("users/{id: $uuidRegex}", ['uses' => 'UserController@show', 'as' => 'users.show']);
    $router->post('users', ['uses' => 'UserController@store', 'as' => 'users.store']);
    $router->put("users/{id:$uuidRegex}/add-emails", ['uses' => 'UserController@addEmails', 'as' => 'users.add-email']);
    $router->put("users/{id:$uuidRegex}/remove-emails", ['uses' => 'UserController@removeEmails', 'as' => 'users.delete-email']);
    $router->put("users/{id:$uuidRegex}/change-password", ['uses' => 'UserController@changePassword', 'as' => 'users.change-password']);
    $router->put("users/{id:$uuidRegex}/grant", ['uses' => 'UserController@grant', 'as' => 'users.grant']);
    $router->put("users/{id:$uuidRegex}/revoke", ['uses' => 'UserController@revoke', 'as' => 'users.revoke']);
    $router->delete("users/{id:$uuidRegex}", ['uses' => 'UserController@destroy', 'as' => 'users.destroy']);
    $router->get("users/by-role/{role}", ['uses' => 'UserController@byRole', 'as' => 'users.by-role']);

});
