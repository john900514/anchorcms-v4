<?php

namespace App\Models\Clients;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientDetail extends Model
{
    use CrudTrait, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $connection = 'redfield';

    protected $fillable = ['client_id', 'field', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array'
    ];

    //was lead. figured it was left over from copy pasta. updated to 'video' for consistency and clarity.
    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Client', 'id', 'client_id');
    }
}
