<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Auth::routes();

Route::get('/', function () {
    $data = [];
    $data['title'] = trans('backpack::base.login'); // set the page title
    $data['username'] = backpack_authentication_column();
    return view('welcome', $data);
});

Route::get('/home', function () {
    return redirect('/access');
});

Route::group([
    'prefix' => 'registration',
    'namespace'  => 'App\Http\Controllers\User',
], function () {
    Route::get('/',  'UserRegistrationController@render_complete_registration');
    Route::post('/', 'UserRegistrationController@complete_registration');
});

Route::group([
    'prefix' => 'internal-api',
    'namespace'  => 'App\Http\Controllers\API',
], function () {
    Route::resource('locations', 'Locations\LocationDepartmentsAPIController')->only(['show']);
});

