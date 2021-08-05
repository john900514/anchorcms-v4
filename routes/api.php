<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers',
    ],
    function () { // custom api routes

        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
    });

Route::group([
    'middleware' => 'api',
    'namespace'  => 'App\Http\Controllers\API',
],
    function () { // custom api routes
        Route::group(['prefix' => 'integrations'], function() {
            Route::get('/', 'Integrations\SSOIntegrationsAPIController@index');
            Route::get('/{integration_id}', 'Integrations\SSOIntegrationsAPIController@show');
        });
    });

Route::group([
    'middleware' => 'auth:api',
    'namespace'  => 'App\Actions',
],
    function () { // custom api routes
        Route::group(['prefix' => 'vault'], function() {
            Route::post('/entry', 'Auth\SecretVault\ValidatePassword');
        });

    });
