<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // register the helper functions
        //$this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Load the Backpack helper methods, for convenience.
     */
    public function loadHelpers()
    {
        require_once __DIR__.'/../../helpers/functions.php';
    }
}
