<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers',
], function () { // custom admin routes
    // if not otherwise configured, setup the "my account" routes
    if (config('backpack.base.setup_my_account_routes')) {
        Route::get('edit-account-info', 'User\UserAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::post('edit-account-info', 'User\UserAccountController@postAccountInfoForm')->name('backpack.account.info.store');
        Route::post('change-password', 'User\UserAccountController@postChangePasswordForm')->name('backpack.account.password');
        Route::post('change-sentry', 'User\UserAccountController@postChangeSentryForm')->name('backpack.account.sentry');
    }
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Actions',
], function () { // custom admin routes
    Route::post('/sso', 'Auth\SingleSignOn\GenerateRequest');
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('users', 'UserCrudController');
    Route::crud('clients', 'ClientsCrudController');
}); // this should be the absolute last line of this file