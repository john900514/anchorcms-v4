<?php

namespace App\Models\Auth;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SsoIntegrations extends Model
{
    use Uuid, SoftDeletes;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['name', 'client_id', 'slug', 'base_url', 'default_route',
        'public_key', 'private_key', 'permitted_role', 'permitted_ability', 'active'
    ];

    public function getFillable()
    {
        return $this->fillable;
    }

    public function getSystemUserId()
    {
        return is_null(backpack_user()) ? 'System' : backpack_user()->id;
    }
}
