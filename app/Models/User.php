<?php

namespace App\Models;

use AnchorCMS\Nautical\Models\RedfieldUser;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Silber\Bouncer\Database\HasRolesAndAbilities;
//use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;

class User extends RedfieldUser
{
    use CrudTrait, HasRolesAndAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany('App\Models\UserDetails', 'user_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\UserDetails', 'user_id', 'id');
    }

    public function sentry_auth_token()
    {
        return $this->detail()->whereDetail('sentry_auth_token');
    }

    public function vault_auth_token()
    {
        return $this->detail()->whereDetail('1password-token')
            ->whereActive(true);
    }

    public function location_or_department()
    {
        return $this->detail()->whereDetail('location-department');
    }

    public function timezone()
    {
        return $this->hasOne('App\Models\UserDetails', 'user_id', 'id')
            ->whereDetail('timezone');
    }

    public function is_client_user()
    {
        return $this->detail()->whereDetail('is_client_user')
                ->whereActive(true);
    }
}
