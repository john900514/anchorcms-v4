<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
use Illuminate\Support\Facades\Cache;

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
        Route::post('change-vault-token', 'User\UserAccountController@postChangeVaultTokenForm')->name('backpack.account.vault');

        Route::group(['prefix' => 'vault'], function() {
            Route::get('/', 'CMS\SecretsVault\VaultAccessController@index')->name('secret-vault');
            Route::get('/entry', function() { \Alert::warning('Ooops! Refreshing the page brings you back to the beginning. Your session was probably lost. Try to avoid refreshing while in the vault!')->flash(); return redirect()->route('secret-vault'); });
            Route::get('/vaults', 'CMS\SecretsVault\VaultAccessController@vault_list')->name('list-of-vaults');
            Route::get('/items', function() { \Alert::warning('Ooops! Refreshing the page brings you back to the beginning. Your session was probably lost. Try to avoid refreshing while in the vault!')->flash(); return redirect()->route('secret-vault'); })->name('vault-items');
            Route::post('/lockout', function() { Cache::forget(backpack_user()->id.'-vault-access'); return response(['success' => true], 200); })->name('lockout');
        });
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
    Route::get('/dashboard', 'Auth\Dashboards\LoadDashboardController');
    Route::post('/sso', 'Auth\SingleSignOn\GenerateRequest');

    Route::get('/sso/generator', 'Utility\ShowComingSoon');
    Route::get('/work-tickets', 'Utility\ShowComingSoon');
    Route::get('/analytics', 'Utility\ShowComingSoon');
    Route::get('/products/integrations', 'Utility\ShowComingSoon');
    Route::get('/projects/code-reviews');
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
