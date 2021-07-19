<?php

namespace App\Providers;

use App\Models\AccessControl\Ability;
use App\Models\AccessControl\Role;
use Illuminate\Support\ServiceProvider;
use Silber\Bouncer\BouncerFacade as Bouncer;

class BouncerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Bouncer::useRoleModel(Role::class);
        Bouncer::useAbilityModel(Ability::class);
    }
}
