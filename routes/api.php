<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// guest routes
Route::post('auth/login', 'AuthController@login');
Route::get('auth/social/urls', 'AuthController@providerUrls')
    ->middleware('cache:public,86400'); // 24 hours cache
Route::get('auth/{provider}/url', 'AuthController@providerUrl');
Route::get('auth/login/{provider}', 'AuthController@providerCallback');

// authenticated routes
Route::group(['middleware' => 'auth:api'], function() {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::get('auth/me', 'AuthController@me');
});
